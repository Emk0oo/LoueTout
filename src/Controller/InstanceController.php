<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
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
            12, // limite
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


    #[Route('/instance/{instance}/product/create', name: 'app_instance_product_create')]
    public function product_create(): Response
    {

        $current_instance = $this->globalVariableService->get('current_instance');

        // Ajouter un produit

        $user = $this->userRepository->findOneBy(['email' => 'john.doe@mail.com']);

        $product = new Product();
        $product->setLabel('product-'.uniqid());
        $product->setPrice(1000);
        $product->setDescription('description');
        $product->setInstance($current_instance);
        $product->setRentBy($user);
        // $user = new User();
        // $user->setFirstname('John');
        // $user->setLastname('Doe');
        // $user->setEmail('john.doe@mail.com');
        // $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        // $user->setPhone('0606060606');
        // $user->setAddress('1 rue du test');
        // $this->manager->persist($user);
        // $this->manager->flush();

        $this->manager->persist($product);
        $this->manager->flush();
        
        dd($product);

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
