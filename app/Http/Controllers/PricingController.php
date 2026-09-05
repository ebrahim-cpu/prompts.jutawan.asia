<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * Class PricingController
 *
 * Coordinates premium subscription tiers, pricing plans, Stripe Checkout
 * session initialization, and subscription activation callbacks.
 *
 * @package App\Http\Controllers
 */
class PricingController extends Controller
{
    /**
     * Define the catalog of subscription tiers, pricing in MYR, and duration periods.
     *
     * @return array<string, array{name: string, price: float, price_display: string, duration_days: int|null, description: string, icon: string, popular: bool}>
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
                'duration_days' => null, // null denotes permanent lifetime access
                'description' => 'Bayar sekali, akses selama-lamanya',
                'icon' => '👑',
                'popular' => false,
            ],
        ];
    }

    /**
     * Render the pricing plans landing page.
     *
     * @return View
     */
    public function index(): View
    {
        $plans = self::plans();
        return view('pricing', compact('plans'));
    }

    /**
     * Initiate a Stripe Checkout hosted session for the selected plan.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function checkout(Request $request): RedirectResponse
    {
        $request->validate(['plan' => 'required|in:1day,1week,1month,1year,lifetime']);

        $plans = self::plans();
        $plan = $plans[$request->plan];

        // Ensure Stripe Secret Key is present and configured
        $stripeSecret = config('services.stripe.secret');

        if (!$stripeSecret || str_contains($stripeSecret, 'YOUR_STRIPE')) {
            return redirect()->route('pricing.index')->with('error', 
                'Payment gateway belum dikonfigurasi. Sila tetapkan STRIPE_KEY dan STRIPE_SECRET di fail .env terlebih dahulu. Dapatkan credentials di https://dashboard.stripe.com/apikeys'
            );
        }

        // Initialize Stripe SDK client
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
                        'unit_amount' => (int)($plan['price'] * 100), // In sen / cents
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
     * Process Stripe callback upon customer return after checkout.
     *
     * Validates payment status and upgrades the user's membership tier.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function success(Request $request): RedirectResponse
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

            // Verify the matching user record
            $user = User::find($session->metadata->user_id ?? $session->client_reference_id);

            if (!$user) {
                return redirect()->route('pricing.index')->with('error', 'Pengguna tidak ditemui.');
            }

            // Activate premium privileges
            $this->activatePremium($user, $session->metadata->plan ?? $plan);

            return redirect()->route('dashboard')->with('success', 'Tahniah! Pembayaran berjaya dan akaun anda telah dinaik taraf ke Premium! 🎉');

        } catch (\Exception $e) {
            return redirect()->route('pricing.index')->with('error', 'Ralat pengesahan pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Upgrade user tier and calculate new expiration timestamp.
     *
     * If the user already has an active future expiration date,
     * the new duration is extended cumulatively on top of it.
     *
     * @param  User  $user
     * @param  string  $planKey
     * @return void
     */
    private function activatePremium(User $user, string $planKey): void
    {
        $plans = self::plans();
        $plan = $plans[$planKey] ?? null;

        if (!$plan) return;

        $user->tier = 'premium';

        if ($plan['duration_days'] === null) {
            // Lifetime permanent access
            $user->premium_expires_at = null;
        } else {
            // Extend existing active subscription or start fresh from now
            $startFrom = ($user->premium_expires_at && $user->premium_expires_at->isFuture())
                ? $user->premium_expires_at
                : now();
            $user->premium_expires_at = $startFrom->addDays($plan['duration_days']);
        }

        $user->save();
    }
}
