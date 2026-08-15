<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PricingController extends Controller
{
    /**
     * Plans list with pricing and duration.
     */
    public static function plans(): array
    {
        return [
            '1day' => [
                'name' => '1 Hari',
                'price' => 3.00,
                'price_display' => 'RM 3',
                'duration_days' => 1,
                'description' => 'Akses penuh selama 24 jam',
                'icon' => '⚡',
                'popular' => false,
            ],
            '1week' => [
                'name' => '1 Minggu',
                'price' => 10.00,
                'price_display' => 'RM 10',
                'duration_days' => 7,
                'description' => 'Akses penuh selama 7 hari',
                'icon' => '🔥',
                'popular' => false,
            ],
            '1month' => [
                'name' => '1 Bulan',
                'price' => 29.00,
                'price_display' => 'RM 29',
                'duration_days' => 30,
                'description' => 'Akses penuh selama 30 hari',
                'icon' => '⭐',
                'popular' => true,
            ],
            '1year' => [
                'name' => '1 Tahun',
                'price' => 199.00,
                'price_display' => 'RM 199',
                'duration_days' => 365,
                'description' => 'Akses penuh selama setahun',
                'icon' => '💎',
                'popular' => false,
            ],
            'lifetime' => [
                'name' => 'Seumur Hidup',
                'price' => 499.00,
                'price_display' => 'RM 499',
                'duration_days' => null, // null = lifetime
                'description' => 'Bayar sekali, akses selama-lamanya',
                'icon' => '👑',
                'popular' => false,
            ],
        ];
    }

    /**
     * Show the pricing page.
     */
    public function index()
    {
        $plans = self::plans();
        return view('pricing', compact('plans'));
    }

    /**
     * Handle payment initiation via Stripe Checkout.
     */
    public function checkout(Request $request)
    {
        $request->validate(['plan' => 'required|in:1day,1week,1month,1year,lifetime']);

        $plans = self::plans();
        $plan = $plans[$request->plan];

        // Check if Stripe is configured
        $stripeSecret = config('services.stripe.secret');

        if (!$stripeSecret || str_contains($stripeSecret, 'YOUR_STRIPE')) {
            // Stripe not configured yet — show friendly error
            return redirect()->route('pricing.index')->with('error', 
                'Payment gateway belum dikonfigurasi. Sila tetapkan STRIPE_KEY dan STRIPE_SECRET di fail .env terlebih dahulu. Dapatkan credentials di https://dashboard.stripe.com/apikeys'
            );
        }

        // Create Stripe Checkout Session
        \Stripe\Stripe::setApiKey($stripeSecret);

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'myr',
                        'product_data' => [
                            'name' => 'PromptLib Premium — ' . $plan['name'],
                            'description' => $plan['description'],
                        ],
                        'unit_amount' => (int)($plan['price'] * 100), // Stripe uses cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('pricing.success') . '?plan=' . $request->plan . '&session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('pricing.index'),
                'client_reference_id' => (string) auth()->id(),
                'metadata' => [
                    'plan' => $request->plan,
                    'user_id' => (string) auth()->id(),
                ],
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            return redirect()->route('pricing.index')->with('error', 
                'Ralat semasa memproses pembayaran: ' . $e->getMessage()
            );
        }
    }

    /**
     * Handle successful payment callback.
     */
    public function success(Request $request)
    {
        $plan = $request->query('plan');
        $sessionId = $request->query('session_id');

        if (!$plan || !$sessionId) {
            return redirect()->route('pricing.index')->with('error', 'Maklumat pembayaran tidak lengkap.');
        }

        $stripeSecret = config('services.stripe.secret');

        if (!$stripeSecret || str_contains($stripeSecret, 'YOUR_STRIPE')) {
            return redirect()->route('pricing.index')->with('error', 'Payment gateway belum dikonfigurasi.');
        }

        try {
            \Stripe\Stripe::setApiKey($stripeSecret);
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                return redirect()->route('pricing.index')->with('error', 'Pembayaran tidak berjaya. Sila cuba semula.');
            }

            // Verify the user matches
            $user = User::find($session->metadata->user_id ?? $session->client_reference_id);

            if (!$user) {
                return redirect()->route('pricing.index')->with('error', 'Pengguna tidak ditemui.');
            }

            // Activate premium
            $this->activatePremium($user, $session->metadata->plan ?? $plan);

            return redirect()->route('dashboard')->with('success', 'Tahniah! Pembayaran berjaya dan akaun anda telah dinaik taraf ke Premium! 🎉');

        } catch (\Exception $e) {
            return redirect()->route('pricing.index')->with('error', 'Ralat pengesahan pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Activate premium for a user based on the selected plan.
     */
    private function activatePremium(User $user, string $planKey)
    {
        $plans = self::plans();
        $plan = $plans[$planKey] ?? null;

        if (!$plan) return;

        $user->tier = 'premium';

        if ($plan['duration_days'] === null) {
            // Lifetime
            $user->premium_expires_at = null;
        } else {
            // Extend from current expiry if still active, otherwise from now
            $startFrom = ($user->premium_expires_at && $user->premium_expires_at->isFuture())
                ? $user->premium_expires_at
                : now();
            $user->premium_expires_at = $startFrom->addDays($plan['duration_days']);
        }

        $user->save();
    }
}
