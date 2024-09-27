<?php

namespace App\Controller;

use App\Entity\InstanceSettings;
use App\Entity\Product;
use App\Entity\RentHistory;
use App\Form\BookingType;
use App\Repository\InstanceRepository;
use App\Repository\ProductRepository;
use App\Repository\RentHistoryRepository;
use App\Repository\UserRepository;
use App\Services\GlobalVariableService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        private RentHistoryRepository $rentHistoryRepository,
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
    public function product_detail(Request $request, Product $product): Response
    {

        $form = $this->createForm(BookingType::class);
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
            
            if($this->rentHistoryRepository->isReservationOngoing($form->get('startDate')->getData(), $form->get('endDate')->getData())) {
                $this->addFlash('danger', 'Le produit est déjà réservé pour cette période');
            } else {
                
                $startedAt  = $form->get('startDate')->getData();
                $endedAt = $form->get('endDate')->getData();

                $startedAt = DateTimeImmutable::createFromMutable($startedAt);
                $endedAt = DateTimeImmutable::createFromMutable($endedAt);

                $rentHistory = new RentHistory();
                $rentHistory->setProduct($product);
                $rentHistory->setRentBy($this->getUser());
                $rentHistory->setStartedAt($startedAt);
                $rentHistory->setEndedAt($endedAt);
                $rentHistory->setPrice(100);
                $rentHistory->setInstance($this->globalVariableService->get('current_instance'));
                $this->manager->persist($rentHistory);
                $this->manager->flush();
                $this->addFlash('success', 'Réservation enregistrée');

                return $this->redirectToRoute('app_instance_product_details', ['instance' => $this->globalVariableService->get('current_instance')->getName(), 'product' => $product->getId()]);
            }
        }

        // récupére tous les historique du produit
        $rentHistory = $this->rentHistoryRepository->findBy(['product' => $product]);

        $historyDates = [];
        foreach($rentHistory as $history) {
            $historyDates[] = [
                'from' => $history->getStartedAt()->format('Y-m-d'),
                'to' => $history->getEndedAt()->format('Y-m-d')
            ];
        }

        return $this->render('instance/product.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'disabledDates' => json_encode($historyDates)
        ]);
    }


}
