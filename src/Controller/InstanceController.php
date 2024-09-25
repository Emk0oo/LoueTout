<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\InstanceRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Services\GlobalVariableService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InstanceController extends AbstractController
{
    public function __construct(
        private InstanceRepository $instanceRepository, 
        private readonly ManagerRegistry $registry, 
        private ProductRepository $productRepository, 
        private EntityManagerInterface $manager,
        private GlobalVariableService $globalVariableService
    )
    {
    }

    #[Route('/instance/{instance}', name: 'app_instance')]
    public function index(string $instance): Response
    {
        
        dd($this->productRepository->findAll());

        return $this->render('instance/index.html.twig', [
            'controller_name' => 'InstanceController',
        ]);
    }

    #[Route('/product/{instance}', name: 'app_instance_product')]
    public function product(): Response
    {

        $current_instance = $this->globalVariableService->get('current_instance');

        if($current_instance) {
            // Ajouter un produit
            $product = new Product();
            $product->setLabel('product-'.uniqid());
            $product->setPrice(1000);
            $product->setDescription('description');
            $product->setInstance($current_instance);
    
            $this->manager->persist($product);
            $this->manager->flush();
    
            dd($product);
        }

        return $this->render('instance/index.html.twig', [
            'controller_name' => 'InstanceController',
        ]);
    }


    #[Route('/instance/{instance}/list', name: 'app_instance_list')]
    public function list(): Response
    {

        $products = $this->productRepository->findAll();

        return $this->render('instance/index.html.twig', [
            'controller_name' => 'InstanceController',
            'products' => $products
        ]);
    }

}
