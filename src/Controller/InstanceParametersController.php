<?php

namespace App\Controller;

use App\Entity\InstanceSettings;
use App\Form\ModifyInstanceParametersType;
use App\Repository\InstanceSettingsRepository;
use App\Services\GlobalVariableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class InstanceParametersController extends AbstractController
{
    #[Route('/instance/{instance}/parameters', name: 'app_instance_parameters')]
    #[IsGranted("ROLE_ADMIN", message: 'You are not allowed to access the admin dashboard.')]
    public function index(GlobalVariableService $globalVariableService, Request $request, InstanceSettingsRepository $instanceSettingsRepository, EntityManagerInterface $em): Response
    {
        $current_instance = $globalVariableService->get('current_instance');
        $instance_settings = $instanceSettingsRepository->findBy([
            'instance' => $current_instance
        ]);
        
        $accent = "";
        $secondary = "";
        
        foreach($instance_settings as $setting) {
            if($setting->getKey() === 'accent') {
                $accent = $setting->getValue();
            }
            if($setting->getKey() === 'secondary') {
                $secondary = $setting->getValue();
            }
        }

        $form = $this->createForm(ModifyInstanceParametersType::class, null, [
            'data' => [
                'accent' => $accent,
                'secondary' => $secondary
            ]
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            $accentToModify = $instanceSettingsRepository->findOneBy([
                'instance' => $current_instance,
                'key' => 'accent'
            ]);
            $accentToModify->setValue($data['accent']);

            $secondaryToModify = $instanceSettingsRepository->findOneBy([
                'instance' => $current_instance,
                'key' => 'secondary'
            ]);
            $secondaryToModify->setInstance($current_instance)->setKey('secondary')->setValue($data['secondary']);
            
            $em->persist($accentToModify);
            $em->persist($secondaryToModify);
            $em->flush();

            return $this->redirectToRoute('app_instance', ['instance' => $current_instance->getName()]);
        }

        return $this->render('instance_parameters/index.html.twig', [
            'accent' => $accent,
            'secondary' => $secondary,
            'form' => $form->createView(),
        ]);
    }
}
