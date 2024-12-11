<?php

namespace App\Notifications\Subscriptions;

use App\Models\BoostPlan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ProfileBoostNotice extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly User $user, private readonly BoostPlan $plan) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $language = App::getLocale();

        return (new MailMessage)
            ->subject(GoogleTranslate::trans('Profile Boost', $language, 'en'))
            ->markdown("mails.$language.boostProfile", [
                'name' => $this->user->name,
                'price' => $this->plan->price,
                'days' => $this->plan->period,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
