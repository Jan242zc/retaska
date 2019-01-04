<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @Route("/eshop/homepage")
 */
class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(ProductRepository $productRepository, SessionInterface $session, Request $request): Response
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
        
        return $this->render('feeshop/homepage.html.twig', ['products' => $productRepository->getTop3(), 
        'searchForm' => $searchForm->createView()
        ]);
    }
    
    /**
     * @Route("/search", name="search")
     */
    public function search(EntityManagerInterface $entityManager, 
    SessionInterface $session, ProductRepository $productRepository, 
    CategoryRepository $categoryRepository, Request $request): Response
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
        
        $results = $entityManager // SQL: SELECT p FROM product p WHERE p.name LIKE '%taska%'
          ->createQuery("SELECT p FROM App\Entity\Product p WHERE p.name LIKE :search")
          ->setParameter('search', "%$search%")
          ->getResult();

        return $this->render('feeshop/search.html.twig', [
        'results' => $results, 
        'searchForm' => $searchForm->createView()
        ]);
    }
}