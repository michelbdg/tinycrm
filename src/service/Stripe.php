<?php

namespace App\Service;

use Stripe\Checkout\Session;



class Stripe
{
    public function makePayment($apiKey, $amount, $product, $email)
    {
        Stripe::setApiKey($apiKey);
        header('Content-Type: application/json');

        $YOUR_DOMAIN = 'http://127.0.0.1:8001';

        $checkout_session = Session::create([
            'customer_email' => $email,
            'submit_type' => 'Payer',
            'billing_address_collection' => 'required',
            'line_items' => [[
                'name' => $product,
                'price' => $amount,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/payment/success',
            'cancel_url' => $YOUR_DOMAIN . '/payment/cancel',
            'automatic_tax' => [
                'enabled' => true,
            ],
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
    }
}