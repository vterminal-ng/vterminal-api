<?php

namespace App\Notifications;

use App\Models\Code;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CodeActivated extends Notification
{
    use Queueable;

    protected $code;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Code $code)
    {
        $this->code = $code;
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
            ->greeting('vTerminal | Code Activated')
            ->line('Hello ' . $this->code->customer->userDetail->fullname . ',')
            ->line('Your transaction code has been activated')
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
