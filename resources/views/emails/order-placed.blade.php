@php
    $gold = '#b8915a'; $ink = '#13171c'; $muted = '#6b7178'; $line = '#ece6db'; $paper = '#f6f3ee';
    $first = \Illuminate\Support\Str::before($order->customer_name, ' ');
@endphp
@component('emails.layout', [
    'title' => ($forAdmin ? 'New order ' : 'Order confirmed ').$order->order_number,
    'preheader' => $forAdmin ? $order->customer_name.' placed an order of '.money($order->total) : 'Thank you! Your order '.$order->order_number.' has been received.',
])
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
  <tr><td class="px" align="center" style="padding:38px 44px 8px;">
    <p style="margin:0;font-size:11px;letter-spacing:3px;text-transform:uppercase;font-weight:bold;color:{{ $gold }};">{{ $forAdmin ? 'New order received' : 'Order confirmed' }}</p>
    <h1 style="margin:12px 0 0;font-family:Georgia,'Times New Roman',serif;font-weight:normal;font-size:30px;line-height:38px;color:{{ $ink }};">
      {{ $forAdmin ? money($order->total).' from '.$order->customer_name : 'Thank you, '.$first.'!' }}
    </h1>
    <p style="margin:14px 0 0;font-size:15px;line-height:24px;color:{{ $muted }};">
      @if ($forAdmin)
        Order <strong style="color:{{ $ink }};">{{ $order->order_number }}</strong> was placed {{ $order->created_at->format('d M Y, g:i a') }} · {{ $order->payment_method_label }}.
      @else
        We've received your order and our team will confirm it shortly by phone or WhatsApp. Here's a summary for your records.
      @endif
    </p>
  </td></tr>

  <tr><td class="px" style="padding:26px 44px 6px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:{{ $paper }};border-radius:12px;">
      <tr>
        <td class="stack" style="padding:16px 18px;font-size:12px;color:{{ $muted }};">Order number<br><strong style="font-size:15px;color:{{ $ink }};">{{ $order->order_number }}</strong></td>
        <td class="stack" style="padding:16px 18px;font-size:12px;color:{{ $muted }};">Date<br><strong style="font-size:15px;color:{{ $ink }};">{{ $order->created_at->format('d M Y') }}</strong></td>
        <td class="stack" style="padding:16px 18px;font-size:12px;color:{{ $muted }};">Payment<br><strong style="font-size:15px;color:{{ $ink }};">{{ $order->payment_method_label }}</strong></td>
      </tr>
    </table>
  </td></tr>

  <tr><td class="px" style="padding:20px 44px 0;">
    <p style="margin:0 0 6px;font-size:11px;letter-spacing:2px;text-transform:uppercase;font-weight:bold;color:{{ $muted }};">Your items</p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
      @foreach ($order->items as $item)
        <tr>
          <td width="76" style="padding:14px 0;border-bottom:1px solid {{ $line }};vertical-align:top;">
            @php $img = $item->product?->image_url; if (! $img || str_ends_with($img, '.svg')) { $img = asset('images/favicon-192.png'); } @endphp
            <img src="{{ $img }}" width="64" height="64" alt="" style="display:block;width:64px;height:64px;border-radius:10px;background:{{ $paper }};object-fit:cover;">
          </td>
          <td style="padding:14px 10px;border-bottom:1px solid {{ $line }};vertical-align:top;font-size:14px;line-height:20px;">
            <strong style="color:{{ $ink }};">{{ $item->name }}</strong><br>
            <span style="font-size:12px;color:{{ $muted }};">
              {{ collect([$item->variant_name, $item->lens_name ? 'Lens: '.$item->lens_name : null, 'Qty '.$item->quantity])->filter()->join(' · ') }}
              @if ($item->prescription || $item->prescription_file)<br><span style="color:#0e4d5a;">✓ Prescription {{ $item->prescription_file ? 'uploaded' : 'received' }}</span>@endif
            </span>
          </td>
          <td align="right" style="padding:14px 0;border-bottom:1px solid {{ $line }};vertical-align:top;font-size:14px;font-weight:bold;white-space:nowrap;">{{ money($item->line_total) }}</td>
        </tr>
      @endforeach
    </table>
  </td></tr>

  <tr><td class="px" style="padding:12px 44px 0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:22px;">
      <tr><td style="color:{{ $muted }};padding:3px 0;">Subtotal</td><td align="right">{{ money($order->subtotal) }}</td></tr>
      @if ($order->discount > 0)<tr><td style="color:#0e4d5a;padding:3px 0;">Discount{{ $order->coupon_code ? ' ('.$order->coupon_code.')' : '' }}</td><td align="right" style="color:#0e4d5a;">− {{ money($order->discount) }}</td></tr>@endif
      <tr><td style="color:{{ $muted }};padding:3px 0;">Delivery</td><td align="right">{{ $order->shipping > 0 ? money($order->shipping) : 'Free' }}</td></tr>
      @if ($order->tax > 0)<tr><td style="color:{{ $muted }};padding:3px 0;">Tax</td><td align="right">{{ money($order->tax) }}</td></tr>@endif
      <tr><td style="padding:12px 0 0;border-top:2px solid {{ $ink }};font-weight:bold;font-size:16px;">Total</td><td align="right" style="padding:12px 0 0;border-top:2px solid {{ $ink }};font-family:Georgia,serif;font-size:22px;">{{ money($order->total) }}</td></tr>
    </table>
  </td></tr>

  <tr><td class="px" style="padding:26px 44px 0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td class="stack" width="50%" style="vertical-align:top;padding:0 10px 16px 0;font-size:13px;line-height:20px;color:{{ $ink }};">
          <p style="margin:0 0 6px;font-size:11px;letter-spacing:2px;text-transform:uppercase;font-weight:bold;color:{{ $muted }};">Delivering to</p>
          <strong>{{ $order->customer_name }}</strong><br>{{ $order->shipping_line1 }}@if ($order->shipping_line2)<br>{{ $order->shipping_line2 }}@endif<br>{{ $order->shipping_city }}{{ $order->shipping_province ? ', '.$order->shipping_province : '' }}<br>{{ $order->customer_phone }}
        </td>
        <td class="stack" width="50%" style="vertical-align:top;padding:0 0 16px 10px;font-size:13px;line-height:20px;color:{{ $ink }};">
          <p style="margin:0 0 6px;font-size:11px;letter-spacing:2px;text-transform:uppercase;font-weight:bold;color:{{ $muted }};">{{ $forAdmin ? 'Contact' : 'What happens next' }}</p>
          @if ($forAdmin)
            {{ $order->customer_email }}<br>{{ $order->customer_phone }}@if ($order->notes)<br><br><em style="color:{{ $muted }};">“{{ $order->notes }}”</em>@endif
          @else
            1. We confirm your order<br>2. {{ collect($order->items)->contains(fn ($i) => $i->lens_name) ? 'Lenses are fitted & checked' : 'We pack & dispatch' }}<br>3. Delivered in 2–5 working days
          @endif
        </td>
      </tr>
    </table>
  </td></tr>

  @if (! $forAdmin && $order->payment_method === 'bank_transfer' && setting('payment_bank_details'))
    <tr><td class="px" style="padding:0 44px 10px;">
      <div style="background:#fbf6ee;border:1px solid #efe1c9;border-radius:12px;padding:16px 18px;font-size:13px;line-height:20px;">
        <strong>Bank transfer details</strong><br>{!! nl2br(e(setting('payment_bank_details'))) !!}<br>Please use <strong>{{ $order->order_number }}</strong> as the reference and share the receipt on WhatsApp.
      </div>
    </td></tr>
  @endif

  <tr><td align="center" class="px" style="padding:20px 44px 40px;">
    <a href="{{ $forAdmin ? route('admin.orders.show', $order) : route('track') }}" style="display:inline-block;background:{{ $ink }};color:#ffffff;text-decoration:none;font-size:14px;font-weight:bold;letter-spacing:.5px;padding:15px 34px;border-radius:999px;">{{ $forAdmin ? 'Open order in admin' : 'Track your order' }}</a>
    @unless ($forAdmin)<p style="margin:16px 0 0;font-size:12px;color:{{ $muted }};">Questions? Simply reply to this email or message us on WhatsApp.</p>@endunless
  </td></tr>
</table>
@endcomponent
