<?php
namespace App\Controller;

use App\Entity\Product;
use App\Entity\Order;
use App\Form\OrderType2;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order2")
 */
class OrderController2 extends AbstractController
{
    /**
     * @Route("/", name="order", methods="POST|GET")
     */
    public function new(Request $request): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType2::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('feeshop/showproducts.html.twig');
        }

        return $this->render('feeshop/order.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }
}