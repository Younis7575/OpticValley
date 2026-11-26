<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        return view('store.account.dashboard', [
            'recent' => $user->orders()->latest()->take(5)->get(),
            'stats' => [
                'orders' => $user->orders()->count(),
                'active' => $user->orders()->whereIn('status', ['pending', 'confirmed', 'processing', 'shipped'])->count(),
                'wishlist' => $user->wishlist()->count(),
                'addresses' => $user->addresses()->count(),
            ],
        ]);
    }

    public function orders(Request $request)
    {
        return view('store.account.orders', ['orders' => $request->user()->orders()->withCount('items')->latest()->paginate(10)]);
    }

    public function order(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return view('store.account.order', ['order' => $order->load(['items.product.primaryImage', 'histories'])]);
    }

    public function cancelOrder(Request $request, Order $order, OrderService $service)
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        if (! $order->canBeCancelled()) {
            return back()->with('error', 'This order can no longer be cancelled. Please contact us.');
        }
        $service->changeStatus($order, 'cancelled', 'Cancelled by customer');

        return back()->with('success', 'Your order has been cancelled.');
    }

    public function wishlist(Request $request)
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        return view('store.account.wishlist', ['products' => $request->user()->wishlist()->with(['images', 'brand'])->latest('wishlists.created_at')->get()]);
    }

    public function toggleWishlist(Request $request, Product $product)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Please sign in to use your wishlist.'], 401);
        }
        $result = $user->wishlist()->toggle($product->id);
        $added = ! empty($result['attached']);

        return response()->json(['in_wishlist' => $added, 'message' => $added ? 'Saved to wishlist' : 'Removed from wishlist']);
    }

    public function profile(Request $request)
    {
        return view('store.account.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => ['required', 'email', 'max:160', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{10,20}$/'],
        ]);
        $user->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);
        $request->user()->update(['password' => $request->password]);

        return back()->with('success', 'Password changed.');
    }

    public function addresses(Request $request)
    {
        return view('store.account.addresses', ['addresses' => $request->user()->addresses()->orderByDesc('is_default')->get()]);
    }

    public function storeAddress(Request $request)
    {
        $data = $this->validateAddress($request);
        $user = $request->user();
        if (! empty($data['is_default']) || ! $user->addresses()->exists()) {
            $user->addresses()->update(['is_default' => false]);
            $data['is_default'] = true;
        }
        $user->addresses()->create($data);

        return back()->with('success', 'Address saved.');
    }

    public function updateAddress(Request $request, Address $address)
    {
        abort_unless($address->user_id === $request->user()->id, 404);
        $data = $this->validateAddress($request);
        if ($data['is_default'] ?? false) {
            $request->user()->addresses()->update(['is_default' => false]);
        }
        $address->update($data);

        return back()->with('success', 'Address updated.');
    }

    public function destroyAddress(Request $request, Address $address)
    {
        abort_unless($address->user_id === $request->user()->id, 404);
        $address->delete();

        return back()->with('success', 'Address removed.');
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'label' => 'nullable|string|max:40',
            'name' => 'required|string|max:120',
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{10,20}$/'],
            'line1' => 'required|string|max:200',
            'line2' => 'nullable|string|max:200',
            'city' => 'required|string|max:80',
            'province' => 'nullable|string|max:80',
            'postal_code' => 'nullable|string|max:20',
            'is_default' => 'nullable|boolean',
        ]) + ['label' => 'Home'];
    }
}
