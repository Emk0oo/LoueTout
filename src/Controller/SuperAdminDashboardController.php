<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SuperAdminDashboardController extends AbstractController
{
    #[IsGranted("ROLE_SUPER_ADMIN", message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/super-admin/dashboard', name: 'app_super_admin_dashboard')]
    public function index(): Response
    {
        return $this->render('super_admin_dashboard/index.html.twig', [
            'controller_name' => 'SuperAdminDashboardController',
        ]);
    }
}
