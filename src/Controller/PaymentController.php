<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\StripeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AbstractController
{
    private $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }
    #[Route('/create-payment-intent', name: 'app_payment_intent')]
    public function createPaymentIntent(Request $request): JsonResponse
    {
        $amount = $request->get('amount');
        $currency = $request->get('currency', 'eur'); // default to EUR
        
        $paymentIntent = $this->stripeService->createPaymentIntent($amount * 100, $currency);

        return new JsonResponse([
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }


    #[Route('/{instance}/payment', name: 'app_payment')]
    public function paymentPage(): Response
    {
        return $this->render('payment/payment.html.twig', [
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'],
        ]);
    }
}
