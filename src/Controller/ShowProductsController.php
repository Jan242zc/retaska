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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @Route("/eshop/products")
 */
class ShowProductsController extends AbstractController
{
    /**
     * @Route("/showproducts", name="showproducts", methods="GET|POST")
     */
    public function showProducts(ProductRepository $productRepository, CategoryRepository $categoryRepository, 
    SessionInterface $session, Request $request): Response
    {
        //VYHLEDÁVACÍ FORMULÁŘ
        $session->start();
        $search = $session->get('search', '');
        $searchForm = $this->createFormBuilder()
            ->add('search', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Vyhledat'])
            ->getForm();
        
        $searchForm->handleRequest($request);
        
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $formData = $searchForm->getData();
            $search = $formData['search'];
            $session->set('search', $search);
            
            return $this->redirectToRoute('search');
        }
        //KONEC VYHLEDÁVACÍHO FORMULÁŘE
        
        return $this->render('feeshop/showproducts.html.twig', ['searchForm' => $searchForm->createView(), 
        'argumenty' => [
        'products' => $productRepository->findAll(),
        'categories' => $categoryRepository->findAll()
        ]]);
    }
    
    /**
     * @Route("/showbycategory/{category}", name="showproducts_category", methods="GET|POST")
     */
    public function showByCategory(Category $category, CategoryRepository $categoryRepository, 
    SessionInterface $session, Request $request): Response
    {
        //VYHLEDÁVACÍ FORMULÁŘ
        $session->start();
        $search = $session->get('search', '');
        $searchForm = $this->createFormBuilder()
            ->add('search', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Vyhledat'])
            ->getForm();
        
        $searchForm->handleRequest($request);
        
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $formData = $searchForm->getData();
            $search = $formData['search'];
            $session->set('search', $search);
            
            return $this->redirectToRoute('search');
        }
        //KONEC VYHLEDÁVACÍHO FORMULÁŘE
        
        return $this->render('feeshop/showbycategory.html.twig', ['searchForm' => $searchForm->createView(),
        'argumenty' => [
        'products' => $category->getProducts(),
        'categories' => $categoryRepository->findAll(),
        'categoryName' => $category->getName()
        ]]);
    }
    /**
     * @Route("/thx", name="thanks")
     */
    public function thx(SessionInterface $session, Request $request): Response
    {
        //VYHLEDÁVACÍ FORMULÁŘ
        $session->start();
        $search = $session->get('search', '');
        $searchForm = $this->createFormBuilder()
            ->add('search', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Vyhledat'])
            ->getForm();
        
        $searchForm->handleRequest($request);
        
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $formData = $searchForm->getData();
            $search = $formData['search'];
            $session->set('search', $search);
            
            return $this->redirectToRoute('search');
        }
        //KONEC VYHLEDÁVACÍHO FORMULÁŘE
        
        return $this->render('feeshop/thanks.html.twig', ['searchForm' => $searchForm->createView()]);
    }
    /**
     * @Route("/{id}", name="showproducts_detail")
     */
    public function showProductDetail(Product $product, SessionInterface $session, Request $request): Response
    {
        //VYHLEDÁVACÍ FORMULÁŘ
        $session->start();
        $search = $session->get('search', '');
        $searchForm = $this->createFormBuilder()
            ->add('search', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Vyhledat'])
            ->getForm();
        
        $searchForm->handleRequest($request);
        
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $formData = $searchForm->getData();
            $search = $formData['search'];
            $session->set('search', $search);
            
            return $this->redirectToRoute('search');
        }
        //KONEC VYHLEDÁVACÍHO FORMULÁŘE
        
        return $this->render('feeshop/showproductdetail.html.twig', ['product' => $product, 'searchForm' => $searchForm->createView()]);
    }
}