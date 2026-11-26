<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        return view('admin.coupons.index', ['coupons' => Coupon::latest()->paginate(25)]);
    }

    public function create()
    {
        return view('admin.coupons.form', ['coupon' => new Coupon(['type' => 'percent', 'is_active' => true, 'min_order' => 0])]);
    }

    public function store(Request $request)
    {
        $c = Coupon::create($this->validated($request));
        ActivityLog::record('coupon.create', $c, "Created coupon {$c->code}");

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.form', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($this->validated($request, $coupon));
        ActivityLog::record('coupon.update', $coupon, "Updated coupon {$coupon->code}");

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon saved.');
    }

    public function destroy(Coupon $coupon)
    {
        ActivityLog::record('coupon.delete', $coupon, "Deleted coupon {$coupon->code}");
        $coupon->delete();

        return back()->with('success', 'Coupon deleted.');
    }

    private function validated(Request $request, ?Coupon $coupon = null): array
    {
        $request->merge(['code' => strtoupper(trim((string) $request->code))]);

        return $request->validate([
            'code' => ['required', 'alpha_dash', 'max:40', Rule::unique('coupons')->ignore($coupon?->id)],
            'type' => 'required|in:percent,fixed',
            'value' => ['required', 'numeric', 'min:0.01', $request->type === 'percent' ? 'max:100' : 'max:9999999'],
            'min_order' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]) + ['is_active' => $request->boolean('is_active'), 'min_order' => (float) $request->input('min_order', 0)];
    }
}
