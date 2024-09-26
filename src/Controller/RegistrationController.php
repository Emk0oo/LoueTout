<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Services\GlobalVariableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Stripe\StripeClient;

class RegistrationController extends AbstractController
{

    public function __construct(
        private GlobalVariableService $globalVariableService,
        private UserRepository $userRepository
        
    ) {
        
    }

    #[Route('instance/{instance}/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($this->userRepository->findOneBy(['email' => $form->get('email')->getData()])) {
                $this->addFlash('error', 'Email already exists');
                return $this->redirectToRoute('app_register', ['instance' => $this->globalVariableService->get('current_instance')->getName()]);
            } 

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setInstance($this->globalVariableService->get('current_instance'));
            
            $stripe = new StripeClient($_ENV['STRIPE_SK']);
            $customer = $stripe->customers->create([
                'email' => $user->getEmail(),
                'name' => $user->getLastname(),
            ]);
            $user->setStripeId($customer->id);


            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_instance', ['instance' => $this->globalVariableService->get('current_instance')->getName()]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
