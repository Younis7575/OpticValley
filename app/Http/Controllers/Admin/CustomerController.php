<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $q = User::whereNull('role_id')->withCount('orders')->withSum(['orders as spent' => fn ($o) => $o->whereNotIn('status', ['cancelled', 'refunded'])], 'total');
        if ($s = $request->query('q')) {
            $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")->orWhere('phone', 'like', "%$s%"));
        }
        if ($request->query('status') !== null && $request->query('status') !== '') {
            $q->where('is_active', $request->query('status') === 'active');
        }

        return view('admin.customers.index', ['customers' => $q->latest()->paginate(25)->withQueryString()]);
    }

    public function show(User $customer)
    {
        abort_unless($customer->isCustomer(), 404);

        return view('admin.customers.show', [
            'customer' => $customer->load('addresses'),
            'orders' => $customer->orders()->latest()->paginate(10),
            'spent' => $customer->orders()->whereNotIn('status', ['cancelled', 'refunded'])->sum('total'),
            'wishlist' => $customer->wishlist()->count(),
        ]);
    }

    public function update(Request $request, User $customer)
    {
        abort_unless($customer->isCustomer(), 404);
        $customer->update(['is_active' => $request->boolean('is_active')]);
        ActivityLog::record('customer.status', $customer, ($customer->is_active ? 'Activated' : 'Blocked')." customer {$customer->email}");

        return back()->with('success', 'Customer updated.');
    }
}
