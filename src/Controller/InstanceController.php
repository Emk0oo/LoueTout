<?php

namespace App\Controller;

use App\Entity\InstanceSettings;
use App\Entity\Product;
use App\Repository\InstanceRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Services\GlobalVariableService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class InstanceController extends AbstractController
{
    public function __construct(
        private InstanceRepository $instanceRepository, 
        private readonly ManagerRegistry $registry, 
        private ProductRepository $productRepository, 
        private UserRepository $userRepository, 
        private EntityManagerInterface $manager,
        private GlobalVariableService $globalVariableService,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    #[Route('/instance/{instance}', name: 'app_instance')]
    public function index(): Response
    {
        
        $products = $this->productRepository->findBy(
            [], // critere
            null, // ordre
            6, // limite
            0 // offset
        );

        return $this->render('instance/index.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/instance/{instance}/products', name: 'app_instance_products')]
    public function products_list(): Response
    {

        $products = $this->productRepository->findAll();

        return $this->render('instance/list.html.twig', [
            'products' => $products
        ]);
    }


    #[Route('/instance/{instance}/setting', name: 'app_instance_setting')]
    public function setting(): Response
    {

        $current_instance = $this->globalVariableService->get('current_instance');

       $instance_setting = new InstanceSettings();
       $instance_setting->setKey('name');
       $instance_setting->setValue('test');

       $current_instance->addInstanceSetting($instance_setting);

        $this->manager->persist($current_instance);
        $this->manager->flush();
        
        dd($current_instance);

        // return $this->render('instance/product.html.twig', [
        //     'product' => $product
        // ]);
    }
    
    #[Route('/instance/{instance}/product/{product}', name: 'app_instance_product_details')]
    public function product_detail(Product $product): Response
    {
        return $this->render('instance/product.html.twig', [
            'product' => $product
        ]);
    }


}
