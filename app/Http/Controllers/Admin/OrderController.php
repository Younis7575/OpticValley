<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q = $this->filtered($request);

        return view('admin.orders.index', [
            'orders' => $q->withCount('items')->latest()->paginate(25)->withQueryString(),
            'counts' => Order::selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status'),
        ]);
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', ['order' => $order->load(['items.product.primaryImage', 'histories.user', 'user'])]);
    }

    public function update(Request $request, Order $order, OrderService $service)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
            'payment_status' => ['required', Rule::in(array_keys(Order::PAYMENT_STATUSES))],
            'courier' => 'nullable|string|max:80',
            'tracking_number' => 'nullable|string|max:80',
            'admin_notes' => 'nullable|string|max:2000',
            'note' => 'nullable|string|max:255',
        ]);
        $order->fill(collect($data)->only(['payment_status', 'courier', 'tracking_number', 'admin_notes'])->all())->save();
        if ($data['status'] !== $order->status) {
            $service->changeStatus($order, $data['status'], $data['note'] ?? null);
        } elseif (! empty($data['note'])) {
            $order->histories()->create(['status' => $order->status, 'note' => $data['note'], 'user_id' => auth()->id()]);
        }
        ActivityLog::record('order.update', $order, "Updated order {$order->order_number}");

        return back()->with('success', 'Order updated.');
    }

    public function refund(Request $request, Order $order, OrderService $service)
    {
        $max = (float) $order->total - (float) $order->refunded_amount;
        $data = $request->validate(['amount' => "required|numeric|min:1|max:$max", 'reason' => 'nullable|string|max:255']);
        $service->refund($order, (float) $data['amount'], $data['reason'] ?? null);

        return back()->with('success', 'Refund recorded.');
    }

    public function invoice(Order $order)
    {
        return view('admin.orders.invoice', ['order' => $order->load('items')]);
    }

    public function prescription(Order $order, OrderItem $item)
    {
        abort_unless($item->order_id === $order->id && $item->prescription_file && Storage::disk('local')->exists($item->prescription_file), 404);

        return Storage::disk('local')->response($item->prescription_file);
    }

    public function export(Request $request)
    {
        $orders = $this->filtered($request)->latest()->get();
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="orders-'.now()->format('Y-m-d').'.csv"'];

        return response()->stream(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Order', 'Date', 'Customer', 'Phone', 'Email', 'City', 'Status', 'Payment', 'Payment status', 'Subtotal', 'Discount', 'Shipping', 'Total', 'Refunded']);
            foreach ($orders as $o) {
                fputcsv($out, [$o->order_number, $o->created_at->format('Y-m-d H:i'), $o->customer_name, $o->customer_phone, $o->customer_email, $o->shipping_city, $o->status, $o->payment_method, $o->payment_status, $o->subtotal, $o->discount, $o->shipping, $o->total, $o->refunded_amount]);
            }
            fclose($out);
        }, 200, $headers);
    }

    private function filtered(Request $request)
    {
        $q = Order::query();
        if ($s = trim((string) $request->query('q'))) {
            $q->where(fn ($w) => $w->where('order_number', 'like', "%$s%")->orWhere('customer_name', 'like', "%$s%")
                ->orWhere('customer_phone', 'like', "%$s%")->orWhere('customer_email', 'like', "%$s%"));
        }
        if ($st = $request->query('status')) {
            $q->where('status', $st);
        }
        if ($p = $request->query('payment')) {
            $q->where('payment_status', $p);
        }
        if ($from = $request->query('from')) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $q->whereDate('created_at', '<=', $to);
        }

        return $q;
    }
}
