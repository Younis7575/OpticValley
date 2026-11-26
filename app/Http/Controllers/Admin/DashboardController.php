<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $days = in_array((int) $request->query('days'), [7, 30, 90, 365], true) ? (int) $request->query('days') : 30;
        $from = now()->subDays($days - 1)->startOfDay();
        $valid = fn ($q) => $q->whereNotIn('status', ['cancelled', 'refunded']);

        $periodOrders = Order::where('created_at', '>=', $from);
        $prevFrom = (clone $from)->subDays($days);
        $revenue = (float) $valid(clone $periodOrders)->sum(DB::raw('total - refunded_amount'));
        $prevRevenue = (float) $valid(Order::whereBetween('created_at', [$prevFrom, $from]))->sum(DB::raw('total - refunded_amount'));

        $daily = $valid(Order::where('created_at', '>=', $from))
            ->selectRaw('DATE(created_at) as d, SUM(total - refunded_amount) as revenue, COUNT(*) as orders')
            ->groupBy('d')->pluck('revenue', 'd');
        $dailyOrders = Order::where('created_at', '>=', $from)->selectRaw('DATE(created_at) as d, COUNT(*) as c')->groupBy('d')->pluck('c', 'd');
        $labels = $rev = $ord = [];
        for ($i = 0; $i < $days; $i++) {
            $d = Carbon::parse($from)->addDays($i)->toDateString();
            $labels[] = Carbon::parse($d)->format($days > 90 ? 'M y' : 'd M');
            $rev[] = round((float) ($daily[$d] ?? 0));
            $ord[] = (int) ($dailyOrders[$d] ?? 0);
        }

        $statusCounts = Order::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');

        $topProducts = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNotIn('orders.status', ['cancelled', 'refunded'])
            ->where('orders.created_at', '>=', $from)
            ->selectRaw('order_items.product_id, order_items.name, SUM(order_items.quantity) as qty, SUM(order_items.line_total) as revenue')
            ->groupBy('order_items.product_id', 'order_items.name')
            ->orderByDesc('qty')->take(6)->get();

        return view('admin.dashboard', [
            'days' => $days,
            'stats' => [
                'revenue' => $revenue,
                'revenue_change' => $prevRevenue > 0 ? round(($revenue - $prevRevenue) / $prevRevenue * 100, 1) : null,
                'orders' => (clone $periodOrders)->count(),
                'aov' => ($n = $valid(clone $periodOrders)->count()) ? $revenue / $n : 0,
                'products' => Product::count(),
                'active_products' => Product::where('is_active', true)->count(),
                'customers' => User::whereNull('role_id')->count(),
                'new_customers' => User::whereNull('role_id')->where('created_at', '>=', $from)->count(),
                'pending' => (int) ($statusCounts['pending'] ?? 0),
                'completed' => (int) ($statusCounts['delivered'] ?? 0),
                'cancelled' => (int) ($statusCounts['cancelled'] ?? 0) + (int) ($statusCounts['refunded'] ?? 0),
                'processing' => (int) ($statusCounts['confirmed'] ?? 0) + (int) ($statusCounts['processing'] ?? 0) + (int) ($statusCounts['shipped'] ?? 0),
            ],
            'chart' => compact('labels', 'rev', 'ord'),
            'recentOrders' => Order::latest()->take(8)->get(),
            'lowStock' => Product::whereColumn('stock', '<=', 'low_stock_threshold')->orderBy('stock')->take(8)->get(),
            'topProducts' => $topProducts,
        ]);
    }
}
