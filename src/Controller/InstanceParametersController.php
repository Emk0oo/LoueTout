<?php

namespace App\Controller;

use App\Services\GlobalVariableService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InstanceParametersController extends AbstractController
{
    public function __construct(
        private GlobalVariableService $globalVariableService
    )
    {
    }

    #[Route('/instance/{instance}/parameters', name: 'app_instance_parameters')]
    public function index(): Response
    {
        $current_instance = $this->globalVariableService->get('current_instance');

        return $this->render('instance_parameters/index.html.twig', [
            'controller_name' => 'InstanceParametersController',
        ]);
    }
}
