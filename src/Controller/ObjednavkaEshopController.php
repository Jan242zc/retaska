<?php
namespace App\Controller;

use App\Entity\Objednavka;
use App\Form\ObjednavkaEshopType;
use App\Repository\ObjednavkaRepository;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Entity\Delivery;
use App\Repository\DeliveryRepository;
use App\Entity\Payment;
use App\Repository\PaymentRepository;
use App\Entity\Country;
use App\Repository\CountryRepository;
use App\Entity\ShoppingBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/eshop")
 */
class ObjednavkaEshopController extends AbstractController
{
    
    /**
     * @Route("/objednavka", name="objednavka_nova", methods="GET|POST")
     */
    public function new(Request $request, ProductRepository $productRepository, DeliveryRepository $deliveryRepository, 
    PaymentRepository $paymentRepository, CountryRepository $countryRepository, SessionInterface $session): Response
    {
        $session->start();
        $basket = $session->get('basket', []);
        $objednavka = new Objednavka();
        $form = $this->createForm(ObjednavkaEshopType::class, $objednavka);
        $form->handleRequest($request);
        
        //NALOŽENÍ KOŠÍKU DO OBJEDNÁVKY
        $shoppingBag = []; //pole pro položky v košíku
        
        //"NALOŽENÍ" ZBOŽÍ DO NÁKUPNÍ TAŠKY, TJ. DO SAMOSTANÉ ENTITY NAVÁZANÉ NA OBJEDNÁVKU
        foreach($basket as $polozka) { //cyklus, samozřejmě, aby to šlo udělat pro jakékoli množství nakupovaných produktů
            $shoppingBag[] = new ShoppingBag( //vytvoří se nový řádek databáze a přes konstruktor se nastaví požadované parametry
            $polozka['id'],
            $polozka['name'],
            $polozka['quantity'],
            $polozka['price']
            );
        }
        
        //VYTVOŘENÍ ZVLÁŠTNÍ PROMĚNNÉ PRO VYKRESLENÍ KOŠÍKU V ŠABLONĚ OBJEDNÁVKOVÉHO FORMULÁŘE
        $goodsForTwig = [];
        foreach ($basket as $polozka => $parametry) {
            foreach($parametry as $parametr => $hodnota) {
                if ($parametr === 'name') {
                    $goodsForTwig[$polozka][$parametr] = $hodnota;
                    }
                if ($parametr === 'quantity') {
                    $goodsForTwig[$polozka][$parametr] = $hodnota;
                    }
                if ($parametr === 'price') {
                    $goodsForTwig[$polozka][$parametr] = $hodnota;
                    }
                }
            }
        
        //ZJIŠTĚNÍ CENY ZA NAKUPOVANÉ ZBOŽÍ DOHROMADY
        $goodsXQuantity = 0;
        $goodsXPrice = 0;
        $goodsTotalPrice = 0;
        foreach($basket as $polozka) {
            $goodsXQuantity = $polozka['quantity'];
            $goodsXPrice = $polozka['price'];
            $goodsTotalPrice = $goodsTotalPrice + ($goodsXQuantity * $goodsXPrice);
        }
        
        //PROMĚNNÁ PRO OVĚŘENÍ MNOŽSTVÍ ZBOŽÍ PŘI DOKONČOVÁNÍ OBJEDNÁVKY
        $enoughStock = false;
        foreach($basket as $polozka){
            $product = $productRepository->find($polozka['id']); //načtení objektu produktu, aby šlo zjistit jeho množství
            if ($polozka['quantity'] <= $product->getStock()){      //samotné ověření
                $enoughStock = true;
            } else {
                $enoughStock = false;
                break;                                              //když je jednoho málo, cyklus se zastaví a proměnná je false
            }
        }
        
        //VAROVÁNÍ O PŘÍLIŠ VYSOKÉM POČTU ZBOŽÍ PRO OBJEDNÁVKOVÝ FORMULÁŘ
        if ($enoughStock){
            $varovani = '';
        } else {
            $varovani = "Vybrali jste příliš vysoký počet kusů. Vraťte se prosím do košíku a upravte množství.";
        }
                
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($enoughStock === true) {                            //pokud je na skladě dost tašek...
            //NASTAVENÍ CENY PRODUKTU A CELKOVÉ CENY TAŠEK
            $numberOfGoods = count($basket);
            for ($i=0; $i < $numberOfGoods; $i++){                  //cyklus pro nasetování kupovaných produktů v entitě objednávky
                $objednavka->addShoppingBag($shoppingBag[$i]);
            }
            $objednavka->setProductPrice($goodsTotalPrice);
            //NASTAVENÍ CENY DODÁNÍ
            $deliveryID = $objednavka->getDelivery();               //zjištění ID dodávky
            $delivery = $deliveryRepository->find($deliveryID);     //dodání objektu dodávky, aby se dala zjistit cena
            $deliveryPrice = $delivery->getPrice();                 //zjištění ceny dodávky
            $objednavka->setDeliveryPrice($deliveryPrice);          //nastavení ceny dodávky pro objednávku
            //NASTAVENÍ CENY PLATBY
            $paymentID = $objednavka->getPayment();                 
            $payment = $paymentRepository->find($paymentID);        
            $paymentPriceCZK = $payment->getPrice();                //zjištění korunové ceny platby
            $paymentPriceEUR = $payment->getPriceEUR();             //zjištění eurové ceny platby
            $countryID = $objednavka->getCountry();                 //zjištění země zákazníky pro zjištění měny ceny platby
            $country = $countryRepository->find($countryID);
            if ($payment->getID() === 1) {                                 //Pokud platba převodem,
                $objednavka->setPaymentPriceCZK(0);                 //je zdarma.
                $objednavka->setPaymentPriceEUR(0);
            } elseif ($payment->getID() === 2) {                           //Pokud platba dobírkou
                if ($country->getID() === 1) {                                 //a zákazník je z ČR,
                    $objednavka->setPaymentPriceCZK($paymentPriceCZK);  //nastaví se cena v korunách,
                    $objednavka->setPaymentPriceEUR(0);                 //eurová je nulová.
                } elseif ($country->getID() === 2) {                           //Pokud je zákazník ze SR, je to obráceně.
                    $objednavka->setPaymentPriceCZK(0);                 
                    $objednavka->setPaymentPriceEUR($paymentPriceEUR);
                }
            }
            //NASTAVENÍ CELKOVÉ CENY
            $totalPrice = $objednavka->getProductPrice() + $deliveryPrice 
            + $objednavka->getPaymentPriceCZK() + ($objednavka->getPaymentPriceEUR() * 25.86);
            $objednavka->setTotalPrice($totalPrice);
            //NASTAVENÍ DATA - ještě je třeba dodělat
            $date = date("Y-m-d H:i:s");                    
            $objednavka->setDate(new \DateTime($date));
            //NASTAVENÍ STAVU OBJEDNÁVKY
            $objednavka->setState('Nová');
            //NASTAVENÍ POČTU ZBOŽÍ NA SKLADĚ
            foreach ($basket as $polozka){      //přes pole košíku, pracuje se mi s ním líp než s polem objektů ($shoppingBag)
                $product = $productRepository->find($polozka['id']); //načtení objektu, aby se dalo upravit množství
                $productOldStock = $product->getStock();    //zjištění počtu na skladě před prodejem
                $productSold = $polozka['quantity'];    //prodané kusy
                $productNewStock = $productOldStock - $productSold; //zjištění nového počtu po vyskladnění
                $product->setStock($productNewStock); //nastavení nového počtu
            }
            foreach ($shoppingBag as $bag){ //příprava objektů v $shoppingBagu pro uložení
                $em->persist($bag);
            }
            $em->persist($objednavka);
            $em->flush();
        } else { //pro případ, že na skladě není dost tašek
            return $this->render('feeshop/objednavka.html.twig', [
                'objednavka' => $objednavka,
                'form' => $form->createView(),
                'productPrice' => $goodsTotalPrice,
                'goodsForTwig' => $goodsForTwig,
                'varovani' => $varovani,
                'enoughStock' => var_dump($enoughStock)
        ]);
            
        }
           //po úspěšném odeslání objednávky
           $session->clear();
           return $this->redirectToRoute('thanks');
        }

        return $this->render('feeshop/objednavka.html.twig', [
            'objednavka' => $objednavka,
            'form' => $form->createView(),
            'goodsForTwig' => $goodsForTwig,
            'productPrice' => $goodsTotalPrice,
            'varovani' => "",
            'enoughStock' => var_dump($enoughStock)
        ]);
    }
}
