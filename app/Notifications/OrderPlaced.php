<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public bool $forAdmin = false) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $o = $this->order->loadMissing('items');
        $mail = (new MailMessage)
            ->subject(($this->forAdmin ? 'New order ' : 'Your OpticValley order ').$o->order_number)
            ->greeting($this->forAdmin ? 'New order received' : 'Thank you, '.$o->customer_name.'!')
            ->line($this->forAdmin
                ? "{$o->customer_name} ({$o->customer_phone}) placed an order of ".money($o->total).'.'
                : 'We have received your order and will confirm it shortly by phone or WhatsApp.');
        foreach ($o->items as $item) {
            $mail->line("• {$item->name}".($item->variant_name ? " ({$item->variant_name})" : '')." × {$item->quantity} — ".money($item->line_total));
        }
        $mail->line('Total: '.money($o->total).' · Payment: '.$o->payment_method_label);

        return $this->forAdmin
            ? $mail->action('Open in admin', route('admin.orders.show', $o))
            : $mail->action('Track your order', route('track'))->line('Order number: '.$o->order_number);
    }
}
