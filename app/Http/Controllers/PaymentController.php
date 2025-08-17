<?php

namespace App\Http\Controllers;

use App\Mail\SubscriptionUpdated;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{


    // protected function sendSubscriptionMail(Subscription $subscription)
    // {
    //     try {
    //         $subscription->load('user');
    //         Mail::to($subscription->user->email)->send(new SubscriptionUpdated($subscription));
    //     } catch (\Exception $e) {
    //         Log::error("Failed to send subscription email for user {$subscription->user->id}. Error: " . $e->getMessage());
    //     }
    // }

    
    public function create(Request $request)
    {
        $request->validate([
            'plan_name'       => 'required|string',
            'amount'          => 'required|numeric|min:0',
            'monitors_limit'  => 'required|integer|min:1',
            'check_interval'  => 'required|integer|min:1',
        ]);

        $userId = auth()->id();

        // Handle Free Plan (no Stripe needed)
        if ((float) $request->amount === 0.0) {
            // Save or update payment record
            Payment::updateOrCreate(
                [
                    'user_id'           => $userId,
                    'plan_name'         => $request->plan_name,
                    'payment_intent_id' => null
                ],
                [
                    'amount'         => 0,
                    'currency'       => 'usd',
                    'monitors_limit' => $request->monitors_limit,
                    'check_interval' => $request->check_interval,
                    'status'         => 'succeeded',
                ]
            );

            // Save or update subscription
            $subscription = Subscription::updateOrCreate(
                ['user_id' => $userId],
                [
                    'plan_name'      => $request->plan_name,
                    'monitors_limit' => $request->monitors_limit,
                    'check_interval' => $request->check_interval,
                    'starts_at'      => now(),
                    'ends_at'        => now()->addMonth(),
                ]
            );

            $this->sendSubscriptionMail($subscription);

            return redirect()->route('dashboard')
                ->with('success', 'Free plan activated successfully.');
        }

        // Paid plans → create Stripe PaymentIntent
        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount'   => (int) ($request->amount * 100), // cents
            'currency' => 'usd',
            'metadata' => [
                'plan_name' => $request->plan_name,
                'user_id'   => $userId,
            ],
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        // Save or update pending payment
        Payment::updateOrCreate(
            ['payment_intent_id' => $intent->id],
            [
                'user_id'        => $userId,
                'plan_name'      => $request->plan_name,
                'amount'         => $request->amount,
                'currency'       => 'usd',
                'monitors_limit' => $request->monitors_limit,
                'check_interval' => $request->check_interval,
                'status'         => 'pending',
            ]
        );

        return view('payment.stripe', [
            'clientSecret' => $intent->client_secret,
            'planName'     => $request->plan_name,
            'amount'       => $request->amount,
        ]);
    }

    public function success(Request $request)
    {
        $paymentIntentId = $request->payment_intent;

        if (!$paymentIntentId) {
            abort(400, 'Payment Intent ID is missing.');
        }

        $payment = Payment::where('payment_intent_id', $paymentIntentId)->first();

        if (!$payment) {
            abort(404, 'Payment record not found.');
        }

        // Update payment status
        $payment->update(['status' => 'succeeded']);

        // Save or update subscription
        $subscription = Subscription::updateOrCreate(
            ['user_id' => $payment->user_id],
            [
                'plan_name'      => $payment->plan_name,
                'monitors_limit' => $payment->monitors_limit,
                'check_interval' => $payment->check_interval,
                'starts_at'      => now(),
                'ends_at'        => now()->addMonth(),
            ]
        );

        // $this->sendSubscriptionMail($subscription);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Payment successful, subscription activated.');
    }
}
