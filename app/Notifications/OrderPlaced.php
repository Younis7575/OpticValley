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
        $o = $this->order->loadMissing('items.product.primaryImage');
        $mail = (new MailMessage)
            ->subject($this->forAdmin ? "New order {$o->order_number} — ".money($o->total) : "Your OpticValley order {$o->order_number} is confirmed")
            ->view('emails.order-placed', ['order' => $o, 'forAdmin' => $this->forAdmin]);

        if ($this->forAdmin) {
            $mail->replyTo($o->customer_email, $o->customer_name);
        } elseif ($reply = setting('store_email')) {
            $mail->replyTo($reply, setting('store_name', 'OpticValley'));
        }

        return $mail;
    }
}
