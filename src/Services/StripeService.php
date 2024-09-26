<?php

namespace App\Services;

use Stripe\StripeClient;

class StripeService
{
    private $stripe;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripe = new StripeClient($stripeSecretKey);
    }

    public function createPaymentIntent(float $amount, string $currency)
    {
        return $this->stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => $currency,
        ]);
    }
}
