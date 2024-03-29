<?php

namespace App\Notifications;

use App\Models\Code;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CodeResolved extends Notification
{
    use Queueable;

    protected $firstname;
    protected $lastname;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($firstname, $lastname)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
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
            ->greeting('vTerminal | Email Updated')
            ->line('Dear ' . $this->firstname . ' ' . $this->lastname)
            ->line('Transaction code has been resolved.')
            ->line('If you do not recognize nor authorize this activity, please contact admin immediately!');
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
