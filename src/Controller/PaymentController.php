<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\StripeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


class PaymentController extends AbstractController
{   
    private $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        Stripe::setApiVersion('2020-08-27');
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


    #[Route('/instance/{instance}/payment/{product}/{length}', name: 'app_payment')]
        public function paymentPage(Product $product, Int $length, string $instance){
            
            
            $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . '/instance/' . $instance;

            $session = Session::create([
                'mode' => 'payment',
                'return_url'=> $baseUrl,
                'ui_mode' => 'embedded',
                'line_items' => [[  
                    'quantity' => $length,
                    'price_data' => [
                        'currency' => 'EUR',
                        'unit_amount' => $product->getPrice() * 100,
                        'product_data' => [
                            'name' => $product->getLabel(),
                        ],
                    ],
                ]],
            ]);
            header("Content-Security-Policy: " .
    "default-src 'self'; " .
    "script-src 'self' https://js.stripe.com https://m.stripe.network; " .
    "style-src 'self' https://fonts.googleapis.com https://m.stripe.network; " .
    "frame-src https://js.stripe.com https://m.stripe.network;"
);
            return $this->render('payment/payment.html.twig', [
                'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'],
                'session' => $session->client_secret,
            ]);
    }
}
