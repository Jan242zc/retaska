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
        $goods= [];
        
        foreach ($basket as $polozka => $parametry){
            foreach ($parametry as $parametr => $hodnota) {
                if ($parametr === 'id') {
                    $goods[$polozka][$parametr] = $hodnota;
                    }
                if ($parametr === 'name') {
                    $goods[$polozka][$parametr] = $hodnota;
                    }
                if ($parametr === 'quantity') {
                    $goods[$polozka][$parametr] = $hodnota;
                    }
                if ($parametr === 'price') {
                    $goods[$polozka][$parametr] = $hodnota;
                    }
                }
            }
        
        //ÚPRAVA KOŠÍKU PRO VYKRESLENÍ V ŠABLONĚ
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
        
        $goodsKeys = (array_keys($goods));
        foreach ($goodsKeys as $key) {
            $goodsXQuantity = $goods[$key]['quantity'];
            $goodsXPrice = $goods[$key]['price'];
            $goodsTotalPrice = $goodsTotalPrice + ($goodsXQuantity * $goodsXPrice);
        }
        
        //ZÍSKÁNÍ OBJEKTŮ NAKUPOVANÝCH PRODUKTŮ, ABY BYLO MOŽNÉ OVĚŘIT A UPRAVIT JEJICH MNOŽSTVÍ NA SKLADĚ
        $arrayOfProducts = [];
        $stockOfProducts = [];
        $numberOfProducts = count($goods);
        for ($i = 0; $i < $numberOfProducts; $i++) {
            $arrayOfProducts[$i] = $productRepository->find($goodsKeys[$i]);
        }
        
        $i = 0;
        
        foreach ($arrayOfProducts as $product) {
            $productXStock = $product->getStock();
            $stockOfProducts[$i] = $productXStock;
            $i++;
        }
        
        $enoughStock = false;
                
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $objednavka->setGoods($goods);
            $objednavka->setProductPrice($goodsTotalPrice);
            //$objednavkaQuantity = $objednavka->getQuantity();
            /*if ($productStock >= $objednavkaQuantity) {
            //NASTAVENÍ CENY PRODUKTU
            $productID = $product->getID();           
            $objednavka->setProduct($productID);         
            $objednavka->setProductPrice($productPrice);*/
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
            //$date = getDate();                    
            //$objednavka->setDate($date);          
            //NASTAVENÍ POČTU ZBOŽÍ NA SKLADĚ
            //$productNewStock = $productStock - $objednavkaQuantity; 
            //$product->setStock($productNewStock);                   
            
            $em->persist($objednavka);
            $em->flush();
        /*} else { //pro případ, že na skladě není dost tašek
            return $this->render('feeshop/objednavka.html.twig', [
                'objednavka' => $objednavka,
                'form' => $form->createView(),
                'productPrice' => $productPrice,
                'varovani' => "Tolik tašek na skladě nemáme. Vyberte prosím menší množství - maximálně ",
                'productName' => $productName,
                'productStock' => $productStock,
                'productPrice' => $productPrice
        ]);
            
        }*/
           //po úspěšném odeslání objednávky
           $session->clear();
           return $this->redirectToRoute('thanks');
        }

        return $this->render('feeshop/objednavka.html.twig', [
            'objednavka' => $objednavka,
            'form' => $form->createView(),
            'goods' => $goods,
            //'stock' => var_dump($stockOfProducts),
            'goodsForTwig' => $goodsForTwig,
            'productPrice' => $goodsTotalPrice,
            //'varovani' => "",
            //'productName' => $productName,
            //'productStock' => "",
            //'productPrice' => $productPrice
        ]);
    }
}
