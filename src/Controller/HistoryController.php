<?php

namespace App\Controller;

use App\Services\GlobalVariableService;
use App\Repository\RentHistoryRepository;
use App\Entity\RentHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HistoryController extends AbstractController
{
    #[Route('instance/{instance}/user/history', name: 'app_history')]
    public function index(EntityManagerInterface $em, RentHistoryRepository $rhr ): Response
    {
        $history= $rhr->findBy(["rentBy"=>$this->getUser()]);
       
        return $this->render('history/index.html.twig', [
            'historyliste' => $history,
            
        ]);
    }
}
