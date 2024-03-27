<?php

namespace App\Providers;

use App\Enums\OtpAction;
use App\Services\OtpService;
use App\Settings\PasswordRuleSettings;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'staff' => \App\Models\Staff::class,
            'idea' => \App\Models\Idea::class,
            'comment' => \App\Models\Comment::class,
        ]);

        Password::defaults(function () {
            $rule = Password::min(8);
            $ruleSetting = app(PasswordRuleSettings::class);

            $rule->max($ruleSetting->max);
            $rule->min($ruleSetting->min);
            if ($ruleSetting->letters) {
                $rule->letters();
            }
            if ($ruleSetting->numbers) {
                $rule->numbers();
            }
            if ($ruleSetting->mixed_case) {
                $rule->mixedCase();
            }
            if ($ruleSetting->symbols) {
                $rule->symbols();
            }

            return $this->app->environment('production')
                        ? $rule
                        : $rule;
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify Your Email Address')
                ->greeting("Welcome {$notifiable->name}")
                ->line('Here is your verification code ')
                ->line((new OtpService($notifiable->email))->generate(OtpAction::EMAIL_VERIFICATION));
        });
    }
}
