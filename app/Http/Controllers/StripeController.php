<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeController extends Controller
{
    public function createIntent(Request $request)
{
    try {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        // Configura tu clave secreta de Stripe desde .env
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $intent = PaymentIntent::create([
            'amount' => (int)$request->amount, // en centavos
            'currency' => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return response()->json([
            'clientSecret' => $intent->client_secret,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error al crear el Intent de pago: ' . $e->getMessage(),
        ], 500);
    }
}
}
