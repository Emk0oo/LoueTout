<?php

namespace App\Controller;

use App\Entity\RentHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HistoryController extends AbstractController
{
    #[Route('instance/{instance}/user/history', name: 'app_history')]
    public function index(EntityManagerInterface $em): Response
    {
        $history = $em->getRepository(RentHistory::class)->findAll();
        return $this->render('history/index.html.twig', [
            'historyliste' => $history,
            
        ]);
    }
}
