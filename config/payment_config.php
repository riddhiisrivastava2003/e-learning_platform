<?php
// Payment Gateway Configuration (Stripe)
// Get these credentials from Stripe Dashboard: https://dashboard.stripe.com/

define('STRIPE_PUBLISHABLE_KEY', 'pk_test_YOUR_STRIPE_PUBLISHABLE_KEY');
define('STRIPE_SECRET_KEY', 'sk_test_YOUR_STRIPE_SECRET_KEY');
define('STRIPE_WEBHOOK_SECRET', 'whsec_YOUR_WEBHOOK_SECRET');

// Currency settings
define('CURRENCY', 'inr');
define('CURRENCY_SYMBOL', '₹');

// Course pricing
define('FREE_COURSE_PRICE', 0);
define('PREMIUM_COURSE_PRICE', 999); // ₹999 for premium courses

// Stripe API functions
function createStripePaymentIntent($amount, $currency = 'inr') {
    require_once 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
    
    try {
        $payment_intent = \Stripe\PaymentIntent::create([
            'amount' => $amount * 100, // Convert to cents
            'currency' => $currency,
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);
        
        return $payment_intent;
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

function confirmStripePayment($payment_intent_id) {
    require_once 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
    
    try {
        $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
        return $payment_intent;
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

// Payment status constants
define('PAYMENT_PENDING', 'pending');
define('PAYMENT_COMPLETED', 'completed');
define('PAYMENT_FAILED', 'failed');
define('PAYMENT_REFUNDED', 'refunded');

// Course enrollment status
define('ENROLLMENT_PENDING', 'pending');
define('ENROLLMENT_ACTIVE', 'active');
define('ENROLLMENT_COMPLETED', 'completed');
define('ENROLLMENT_CANCELLED', 'cancelled');
?> 