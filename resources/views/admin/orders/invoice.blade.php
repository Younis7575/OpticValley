<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><title>Invoice {{ $order->order_number }}</title><meta name="robots" content="noindex">
<style>
    *{box-sizing:border-box}body{font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;color:#13171c;margin:0;padding:40px;font-size:13px}
    .wrap{max-width:780px;margin:auto}.row{display:flex;justify-content:space-between;gap:24px}h1{font-family:Georgia,serif;font-weight:400;font-size:34px;margin:0}
    .muted{color:#6b7178}table{width:100%;border-collapse:collapse;margin-top:28px}th{text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#6b7178;border-bottom:1px solid #e5dfd4;padding:10px 6px}
    td{padding:12px 6px;border-bottom:1px solid #efebe4;vertical-align:top}.r{text-align:right}.tot td{border:0;padding:5px 6px}.grand td{font-size:16px;font-weight:700;border-top:2px solid #13171c;padding-top:10px}
    .brand{font-family:Georgia,serif;font-size:22px}.brand i{color:#b8915a}.box{background:#f6f3ee;border-radius:10px;padding:14px 16px;margin-top:24px}
    @media print{.noprint{display:none}body{padding:0}}
</style>
</head>
<body>
<div class="wrap">
    <p class="noprint" style="text-align:right"><button onclick="window.print()" style="padding:8px 16px;border-radius:8px;border:1px solid #ccc;background:#fff;cursor:pointer">Print / Save PDF</button></p>
    <div class="row">
        <div><img src="{{ asset('images/logo.png') }}" alt="OpticValley" style="height:70px"><p class="muted" style="margin:6px 0 0;max-width:260px">{{ setting('store_address') }}<br>{{ setting('store_phone') }} · {{ setting('store_email') }}</p></div>
        <div style="text-align:right"><h1>Invoice</h1><p class="muted" style="margin:6px 0 0">#{{ $order->order_number }}<br>{{ $order->created_at->format('d M Y') }}</p></div>
    </div>
    <div class="row box">
        <div><strong>Bill / ship to</strong><br>{{ $order->customer_name }}<br>{{ $order->shipping_line1 }}@if ($order->shipping_line2), {{ $order->shipping_line2 }}@endif<br>{{ $order->shipping_city }}{{ $order->shipping_province ? ', '.$order->shipping_province : '' }}<br>{{ $order->customer_phone }}</div>
        <div style="text-align:right"><strong>Payment</strong><br>{{ $order->payment_method_label }}<br>{{ \App\Models\Order::PAYMENT_STATUSES[$order->payment_status] ?? '' }}@if ($order->tracking_number)<br><br><strong>Tracking</strong><br>{{ $order->courier }} {{ $order->tracking_number }}@endif</div>
    </div>
    <table>
        <thead><tr><th>Item</th><th class="r">Price</th><th class="r">Qty</th><th class="r">Total</th></tr></thead>
        <tbody>
            @foreach ($order->items as $i)
                <tr><td><strong>{{ $i->name }}</strong><br><span class="muted">{{ collect(['SKU '.$i->sku, $i->variant_name, $i->lens_name ? 'Lens: '.$i->lens_name : null])->filter()->join(' · ') }}</span></td><td class="r">{{ money($i->unit_price) }}</td><td class="r">{{ $i->quantity }}</td><td class="r">{{ money($i->line_total) }}</td></tr>
            @endforeach
        </tbody>
    </table>
    <table style="width:320px;margin-left:auto;margin-top:12px">
        <tr class="tot"><td class="muted">Subtotal</td><td class="r">{{ money($order->subtotal) }}</td></tr>
        @if ($order->discount > 0)<tr class="tot"><td class="muted">Discount {{ $order->coupon_code ? '('.$order->coupon_code.')' : '' }}</td><td class="r">− {{ money($order->discount) }}</td></tr>@endif
        <tr class="tot"><td class="muted">Shipping</td><td class="r">{{ money($order->shipping) }}</td></tr>
        @if ($order->tax > 0)<tr class="tot"><td class="muted">Tax</td><td class="r">{{ money($order->tax) }}</td></tr>@endif
        <tr class="tot grand"><td>Total</td><td class="r">{{ money($order->total) }}</td></tr>
        @if ($order->refunded_amount > 0)<tr class="tot"><td class="muted">Refunded</td><td class="r">− {{ money($order->refunded_amount) }}</td></tr>@endif
    </table>
    <p class="muted" style="margin-top:48px;text-align:center">Thank you for choosing {{ setting('store_name') }}. Frames carry a 6-month warranty against manufacturing defects.</p>
</div>
</body>
</html>
