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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/eshop/basket")
 */
class BasketController extends AbstractController
{
    /**
     * @Route("/", name="basket_index")
     */
    public function index(SessionInterface $session): Response
    {
        $session->start();
        $basket = $session->get('basket', []);
        
        return $this->render('feeshop/basket_index.html.twig', ['basket' => $basket]);
    } 
    
    /**
     * @Route("/add/{product}", name="basket_add")
     */
    public function add(Request $request, SessionInterface $session, 
    Product $product, ProductRepository $productRepository): Response
    {
        $basket = $session->get('basket', []);
        $quantity = 0;
        $productStock = $product->getStock();
        $form = $this->createFormBuilder()
            ->add('quantity', IntegerType::class)
            ->add('save', SubmitType::class, ['label' => 'Uložit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $quantity = $formData['quantity'];
            if (($quantity <= $productStock) && ($quantity > 0)) {
            $basket[$product->getID()] = [
            'id' => $product->getID(), 'name' => $product->getName(),
            'quantity' => $quantity, 'price' => $product->getPrice()
            ];
            $session->set('basket', $basket);
            $upozorneni = 'Zboží bylo přidáno do košíku!';
            return $this->redirectToRoute('basket_index');
            } elseif ($quantity > $productStock) {
                $varovani = "Tolik kusů na skladě nemáme. Zvolte prosím nižší množství (max. $productStock).";              
            } elseif ($quantity <= 0) {
                $varovani = "Vyberte smysluplný počet kusů.";
            }
            return $this->render('feeshop/basket_add.html.twig', [
                'form' => $form->createView(),
                'quantity' => $quantity ?? null,
                'submittedData' => $formData ?? [],
                'product' => $product,
                'varovani' => $varovani
        ]);
        }
        
        return $this->render('feeshop/basket_add.html.twig', [
            'form' => $form->createView(),
            'quantity' => $quantity ?? null,
            'submittedData' => $formData ?? [],
            'product' => $product,
            'varovani' => ""
        ]);
    }
    
    /**
     * @Route("/delete/{product}", name="basket_delete")
     */
    public function delete(SessionInterface $session, Product $product, ProductRepository $productRepository): Response
    {
        $session->start();
        $basket = $session->get('basket', []);
        unset($basket[$product->getID()]);
        $session->set('basket', $basket);
        
        return $this->redirectToRoute('basket_index');
    } 
}