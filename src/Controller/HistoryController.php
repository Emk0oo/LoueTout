<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HistoryController extends AbstractController
{
    #[Route('/history', name: 'app_history')]
    public function index(): Response
    {
        $history=[
            [
                "brand" => "Toyota",
                "price" => 20000,
                "pickup" => "2023-06-15",
                "return" => "2023-06-30"
            ],
            [
                "brand" => "BMW",
                "price" => 35000,
                "pickup" => "2022-12-01",
                "return" => "2023-00-00"
            ],
            [
                "brand" => "Mercedes",
                "price" => 50000,
                "pickup" => "2021-09-10",
                "return" => "2021-11-10"
            ],
            [
                "brand" => "Audi",
                "price" => 45000,
                "pickup" => "2020-11-05",
                "return" => "2020-11-10"
            ],
            [
                "brand" => "BMW",
                "price" => 35000,
                "pickup" => "2022-12-01",
                "return" => "2023-00-00"
            ],
            [
                "brand" => "Mercedes",
                "price" => 50000,
                "pickup" => "2021-09-10",
                "return" => "2021-11-10"
            ],
            [
                "brand" => "Audi",
                "price" => 45000,
                "pickup" => "2020-11-05",
                "return" => "2020-11-10"
            ],
            [
                "brand" => "BMW",
                "price" => 35000,
                "pickup" => "2022-12-01",
                "return" => "2023-00-00"
            ],
            [
                "brand" => "Mercedes",
                "price" => 50000,
                "pickup" => "2021-09-10",
                "return" => "2021-11-10"
            ],
            [
                "brand" => "Audi",
                "price" => 45000,
                "pickup" => "2020-11-05",
                "return" => "2020-11-10"
            ],
            [
                "brand" => "BMW",
                "price" => 35000,
                "pickup" => "2022-12-01",
                "return" => "2023-00-00"
            ],
            [
                "brand" => "Mercedes",
                "price" => 50000,
                "pickup" => "2021-09-10",
                "return" => "2021-11-10"
            ],
            [
                "brand" => "Audi",
                "price" => 45000,
                "pickup" => "2020-11-05",
                "return" => "2020-11-10"
            ],
            [
                "brand" => "BMW",
                "price" => 35000,
                "pickup" => "2022-12-01",
                "return" => "2023-00-00"
            ],
            [
                "brand" => "Mercedes",
                "price" => 50000,
                "pickup" => "2021-09-10",
                "return" => "2021-11-10"
            ],
            [
                "brand" => "Audi",
                "price" => 45000,
                "pickup" => "2020-11-05",
                "return" => "2020-11-10"
            ]
        ];
        return $this->render('history/index.html.twig', [
            'controller_name' => 'HistoryController',
            'historyliste' => $history,
        ]);
    }
}
