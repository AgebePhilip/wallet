<?php

// app/Notifications/TransactionApprovalNotification.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TransactionApprovalNotification extends Notification
{
    use Queueable;

    protected $transaction;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('A transaction requires your approval.')
            ->line('From Wallet ID: ' . $this->transaction->from_wallet_id)
            ->line('To Wallet ID: ' . $this->transaction->to_wallet_id)
            ->line('Amount: ' . $this->transaction->amount)
            ->action('Approve Transaction', url('/admin/approve/' . $this->transaction->id))
            ->line('Thank you for using our application!');
    }
}

