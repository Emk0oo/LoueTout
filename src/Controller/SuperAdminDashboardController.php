<?php

namespace App\Controller;

use App\Entity\Instance;
use App\Entity\InstanceSettings;
use App\Entity\User;
use App\Form\AddInstanceType;
use App\Repository\InstanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SuperAdminDashboardController extends AbstractController
{
    #[IsGranted("ROLE_SUPER_ADMIN", message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/super-admin/dashboard', name: 'app_super_admin_dashboard')]
    public function index(InstanceRepository $instanceRepository): Response
    {
        $instanceList = $instanceRepository->findAll();

        return $this->render('super_admin_dashboard/index.html.twig', [
            'instanceList' => $instanceList,
        ]);
    }

    #[IsGranted("ROLE_SUPER_ADMIN", message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/super-admin/dashboard/add-instance', name: 'app_super_admin_add_instance')]
    public function addInstance(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hahser, KernelInterface $kernel): Response
    {
        $form = $this->createForm(AddInstanceType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $instance_uuid = uniqid();
            $instance = new Instance();
            $instance->setName($form->get('name')->getData() . '-' . $instance_uuid);

            $em->persist($instance);

            $color1 = new InstanceSettings();
            $color1->setKey('color1')->setValue($form->get('color1')->getData())->setInstance($instance);
            $em->persist($color1);

            $color2 = new InstanceSettings();
            $color2->setKey('color2')->setValue($form->get('color2')->getData())->setInstance($instance);
            $em->persist($color2);

            $color3 = new InstanceSettings();
            $color3->setKey('color3')->setValue($form->get('color3')->getData())->setInstance($instance);
            $em->persist($color3);

            $color4 = new InstanceSettings();
            $color4->setKey('color4')->setValue($form->get('color4')->getData())->setInstance($instance);
            $em->persist($color4);

            $user_admin = new User();
            $user_admin->setEmail($form->get('admin_email')->getData())
                ->setRoles(['ROLE_ADMIN'])
                ->setPassword($hahser->hashPassword($user_admin, $form->get('admin_password')->getData()))
                ->setInstance($instance)
                ->setPhone('0000000000')
                ->setAddress('N/A')
                ->setFirstname('Admin')
                ->setLastname('Admin');
            $em->persist($user_admin);
            
            $em->flush();

            $input = new ArrayInput([
                'command' => 'app:database:create',
                'id' => $instance->getId(),
            ]);

            $application = new Application($kernel);
            $application->setAutoExit(false);
            
            $application->run($input, new NullOutput());

            // Rediriger ou montrer un message de succès
            $this->addFlash('success', 'Instance ajoutée avec succès !');
            return $this->redirectToRoute('app_super_admin_dashboard');
        }

        return $this->render('super_admin_dashboard/add_instance.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
