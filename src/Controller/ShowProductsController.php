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
 * @Route("/products")
 */
class ShowProductsController extends AbstractController
{
    /**
     * @Route("/", name="showproducts", methods="GET|POST")
     */
    public function showProducts(ProductRepository $productRepository): Response
    {
        return $this->render('feeshop/showproducts.html.twig', ['products' => $productRepository->findAll()]);
    }
    /**
     * @Route("/thx", name="thanks")
     */
    public function thx(): Response
    {
        return $this->render('feeshop/thanks.html.twig');
    }
    /**
     * @Route("/{id}", name="showproducts_detail", methods="GET")
     */
    public function showProductDetail(Product $product): Response
    {
        return $this->render('feeshop/showproductdetail.html.twig', ['product' => $product]);
    }
}