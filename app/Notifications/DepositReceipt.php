<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class DepositReceipt extends Notification
{
    use Queueable;

    protected $receipt;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($receipt)
    {
        //
        $this->receipt = $receipt;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('vTerminal | Transaction Receipt.')
            ->line('Your deposit was successful')
            ->line('Amount' . $this->receipt['amount'])
            ->line('Payment Method' . $this->receipt['payment_method'])
            ->line('Date' . $this->receipt['Date'])
            ->line( $this->receipt['reference'])
            ->action('If you do not recognize nor authorize this activity or have questions, please click here!', url('app.vterminal.ng'))
           
            ->line('Thank you for using Vterminal');
         

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
