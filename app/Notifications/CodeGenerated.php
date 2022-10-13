<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CodeGenerated extends Notification
{
    use Queueable;

    protected $code;
    protected $firstname;
    protected $lastname;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($code, $firstname, $lastname)
    {
        $this->code = $code;
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
            ->greeting('vTerminal | Code Generated')
            ->line('Dear ' . $this->firstname . ' ' . $this->lastname)
            ->line('Transaction code {'.$this->code.'} generated successfully.')
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
