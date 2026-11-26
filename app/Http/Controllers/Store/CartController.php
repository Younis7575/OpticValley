<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\LensOption;
use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function index()
    {
        return view('store.checkout.cart', ['cart' => $this->cart, 'lines' => $this->cart->lines()]);
    }

    public function add(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);
        $data = $request->validate([
            'qty' => 'nullable|integer|min:1|max:20',
            'variant_id' => 'nullable|integer',
            'lens_id' => 'nullable|integer',
            'rx' => 'nullable|array',
            'rx.*' => 'nullable|string|max:20',
            'rx_note' => 'nullable|string|max:300',
            'rx_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $variantId = null;
        if (! empty($data['variant_id'])) {
            $variant = $product->variants()->find($data['variant_id']);
            abort_unless($variant, 422, 'Invalid colour selected.');
            $variantId = $variant->id;
        }
        $lensId = null;
        $prescription = null;
        $rxFile = null;
        if (! empty($data['lens_id']) && $product->prescription_enabled) {
            $lens = LensOption::active()->find($data['lens_id']);
            abort_unless($lens, 422, 'Invalid lens selected.');
            $lensId = $lens->id;
            $rx = array_filter($data['rx'] ?? [], fn ($v) => $v !== null && $v !== '');
            if ($lens->requires_prescription && ! $rx && ! $request->hasFile('rx_file')) {
                return $this->fail($request, 'Please enter your prescription or upload a photo of it.');
            }
            $prescription = $rx ? array_merge($rx, ['note' => $data['rx_note'] ?? null]) : null;
            if ($request->hasFile('rx_file')) {
                $rxFile = $request->file('rx_file')->store('prescriptions', 'local');
            }
        }

        $qty = (int) ($data['qty'] ?? 1);
        $available = $variantId ? $product->variants()->find($variantId)->stock : $product->stock;
        if ($available < 1) {
            return $this->fail($request, 'Sorry, this item is out of stock.');
        }

        $this->cart->add($product, min($qty, $available), $variantId, $lensId, $prescription, $rxFile);
        $message = "{$product->name} added to cart";

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout');
        }
        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'count' => $this->cart->count()]);
        }

        return back()->with('success', $message);
    }

    public function update(Request $request, string $key)
    {
        $request->validate(['qty' => 'required|integer|min:0|max:20']);
        $this->cart->update($key, (int) $request->qty);

        return back();
    }

    public function remove(string $key)
    {
        $this->cart->remove($key);

        return back()->with('success', 'Item removed from cart');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string|max:40']);
        if ($error = $this->cart->applyCoupon($request->code)) {
            return back()->with('error', $error);
        }

        return back()->with('success', 'Coupon applied');
    }

    public function removeCoupon()
    {
        $this->cart->removeCoupon();

        return back()->with('success', 'Coupon removed');
    }

    private function fail(Request $request, string $message)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $message], 422)
            : back()->withInput()->with('error', $message);
    }
}
