<?php

namespace App\Controller;

use Stripe\Tax\Settings;
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
    #[Route('/instance/{instance}/create-payment-intent', name: 'app_payment_intent', methods: ['POST'])]
    public function createPaymentIntent(Request $request): JsonResponse
    {
        // Decode the incoming JSON body
        $data = json_decode($request->getContent(), true);

        if (!isset($data['amount'])) {
            return new JsonResponse(['error' => 'Amount is required'], 400);
        }
        $amount = (int) $data['amount'];
        $currency = $data['currency'] ?? 'eur';

        if ($amount <= 0) {
            return new JsonResponse(['error' => 'Invalid amount'], 400);
        }

        try {
            $paymentIntent = $this->stripeService->createPaymentIntent(
                amount: $amount * 100,
                currency: $currency
            );

            return new JsonResponse([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    #[Route('/instance/{instance}/payment', name: 'app_payment')]
    public function paymentPage(): Response
    {
        return $this->render('payment/payment.html.twig', [
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'],
        ]);
    }
}
