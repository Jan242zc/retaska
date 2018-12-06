<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/homepage")
 */
class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage", methods="GET|POST")
     */
    public function homepage(ProductRepository $productRepository): Response
    {
        return $this->render('feeshop/homepage.html.twig', ['products' => $productRepository->getTop3()]);
    }
}