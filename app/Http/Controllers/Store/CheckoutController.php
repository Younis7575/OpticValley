<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Notifications\OrderPlaced;
use App\Services\Cart;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function show(Request $request)
    {
        if ($this->cart->lines()->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('store.checkout.checkout', [
            'cart' => $this->cart,
            'lines' => $this->cart->lines(),
            'methods' => $this->paymentMethods(),
            'address' => $request->user()?->addresses()->orderByDesc('is_default')->first(),
        ]);
    }

    public function store(Request $request, OrderService $orders)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:160',
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{10,20}$/'],
            'line1' => 'required|string|max:200',
            'line2' => 'nullable|string|max:200',
            'city' => 'required|string|max:80',
            'province' => 'nullable|string|max:80',
            'postal_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'payment_method' => ['required', Rule::in(array_keys($this->paymentMethods()))],
            'save_address' => 'nullable|boolean',
        ], ['phone.regex' => 'Please enter a valid phone number.']);

        $order = $orders->place($data, $request->user());

        if ($request->user() && $request->boolean('save_address') && ! $request->user()->addresses()->exists()) {
            $request->user()->addresses()->create([
                'name' => $data['name'], 'phone' => $data['phone'], 'line1' => $data['line1'], 'line2' => $data['line2'] ?? null,
                'city' => $data['city'], 'province' => $data['province'] ?? null, 'postal_code' => $data['postal_code'] ?? null, 'is_default' => true,
            ]);
        }

        $this->cart->clear();
        session()->push('placed_orders', $order->order_number);

        try {
            Notification::route('mail', $order->customer_email)->notify(new OrderPlaced($order));
            if ($admin = Setting::get('store_email')) {
                Notification::route('mail', $admin)->notify(new OrderPlaced($order, true));
            }
        } catch (\Throwable $e) {
            Log::warning('Order email failed: '.$e->getMessage());
        }

        return redirect()->route('checkout.thanks', $order);
    }

    public function thankYou(Request $request, Order $order)
    {
        $allowed = in_array($order->order_number, session('placed_orders', []), true)
            || ($request->user() && $order->user_id === $request->user()->id);
        abort_unless($allowed, 404);

        return view('store.checkout.thanks', ['order' => $order->load('items')]);
    }

    private function paymentMethods(): array
    {
        $m = [];
        if (Setting::get('payment_cod_enabled', '1') === '1') {
            $m['cod'] = ['Cash on Delivery', 'Pay in cash when your order arrives.'];
        }
        if (Setting::get('payment_bank_enabled', '0') === '1') {
            $m['bank_transfer'] = ['Bank Transfer / Easypaisa / JazzCash', 'Transfer to our account; we confirm and dispatch.'];
        }
        if (Setting::get('payment_online_enabled', '0') === '1') {
            $m['online'] = [Setting::get('payment_online_label', 'Debit / Credit Card'), 'You will be contacted with a secure payment link.'];
        }

        return $m ?: ['cod' => ['Cash on Delivery', 'Pay in cash when your order arrives.']];
    }
}
