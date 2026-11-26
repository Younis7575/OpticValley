@php
    $steps = ['pending' => 'Placed', 'confirmed' => 'Confirmed', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered'];
    $keys = array_keys($steps);
    $current = array_search($order->status, $keys, true);
@endphp
@if (in_array($order->status, ['cancelled', 'refunded'], true))
    <div class="rounded-2xl bg-sale/10 p-5 text-sm font-semibold text-sale">This order was {{ $order->status }}{{ $order->cancelled_at ? ' on '.$order->cancelled_at->format('d M Y') : '' }}.</div>
@else
    <ol class="grid grid-cols-5 gap-2">
        @foreach ($steps as $k => $label)
            @php $done = $current !== false && array_search($k, $keys, true) <= $current; @endphp
            <li class="text-center">
                <div class="h-1.5 rounded-full {{ $done ? 'bg-brand' : 'bg-line' }}"></div>
                <p class="mt-2 text-[11px] font-semibold {{ $done ? 'text-ink' : 'text-muted' }}">{{ $label }}</p>
            </li>
        @endforeach
    </ol>
    @if ($order->tracking_number)
        <p class="mt-4 rounded-2xl bg-sand p-4 text-sm"><strong>{{ $order->courier ?: 'Courier' }}</strong> tracking number: <span class="font-mono">{{ $order->tracking_number }}</span></p>
    @endif
@endif
