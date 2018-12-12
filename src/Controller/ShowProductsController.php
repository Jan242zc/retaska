<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/eshop/products")
 */
class ShowProductsController extends AbstractController
{
    /**
     * @Route("/showbycategory/{category}", name="showproducts", methods="GET|POST")
     */
    public function showProducts(Category $category, CategoryRepository $categoryRepository): Response
    {
        return $this->render('feeshop/showproducts.html.twig', ['argumenty' => [
        'products' => $category->getProducts(),
        'categories' => $categoryRepository->findAll()
        ]]);
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