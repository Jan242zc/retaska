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

/**
 * @Route("/eshop")
 */
class ObjednavkaEshopController extends AbstractController
{
    
    /**
     * @Route("/objednavka", name="objednavka_nova", methods="GET|POST")
     */
    public function new(Request $request, Product $product, DeliveryRepository $deliveryRepository, 
    PaymentRepository $paymentRepository, CountryRepository $countryRepository): Response
    {
        $objednavka = new Objednavka();
        $form = $this->createForm(ObjednavkaEshopType::class, $objednavka);
        $form->handleRequest($request);
        
        $productPrice = $product->getPrice();
        $productStock = $product->getStock();
        $productName = $product->getName();
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $objednavkaQuantity = $objednavka->getQuantity();
            if ($productStock >= $objednavkaQuantity) {
            //NASTAVENÍ CENY PRODUKTU
            $productID = $product->getID();           
            $objednavka->setProduct($productID);         
            $objednavka->setProductPrice($productPrice);
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
            $totalPrice = ($productPrice * $objednavkaQuantity) + $deliveryPrice 
            + $objednavka->getPaymentPriceCZK() + ($objednavka->getPaymentPriceEUR() * 25.86);
            $objednavka->setTotalPrice($totalPrice);
            //NASTAVENÍ DATA - ještě je třeba dodělat
            //$date = getDate();                    
            //$objednavka->setDate($date);          
            //NASTAVENÍ POČTU ZBOŽÍ NA SKLADĚ
            $productNewStock = $productStock - $objednavkaQuantity; 
            $product->setStock($productNewStock);                   
            
            $em->persist($objednavka);
            $em->flush();
        } else { //pro případ, že na skladě není dost tašek
            return $this->render('feeshop/objednavka.html.twig', [
                'objednavka' => $objednavka,
                'form' => $form->createView(),
                'productPrice' => $productPrice,
                'varovani' => "Tolik tašek na skladě nemáme. Vyberte prosím menší množství - maximálně ",
                'productName' => $productName,
                'productStock' => $productStock,
                'productPrice' => $productPrice
        ]);
            
        }
           //po úspěšném odeslání objednávky
            return $this->redirectToRoute('thanks');
        }

        return $this->render('feeshop/objednavka.html.twig', [
            'objednavka' => $objednavka,
            'form' => $form->createView(),
            'productPrice' => $productPrice,
            'varovani' => "",
            'productName' => $productName,
            'productStock' => "",
            'productPrice' => $productPrice
        ]);
    }
}
