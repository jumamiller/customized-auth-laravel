<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegisteredSuccessfully extends Notification
{
    use Queueable;

    /**
     * @var user
     */
    protected $user;

    /**
     * Create a new notification instance.
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user=$user;
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
        $user = $this->user;
        return (new MailMessage)
                    ->from(env('ADMIN_MAIL'))
                    ->subject('successfully created new account')
                    ->greeting(sprintf('Hello %s',$user->name))
                    ->line('You have successfully registered on Bikebuka.Please activate your account to enjoy unlimited rides')
                    ->action('click Here',route('activate.user',$user->activation_code))
                    ->line('Thank you for using BikeBuka');
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
