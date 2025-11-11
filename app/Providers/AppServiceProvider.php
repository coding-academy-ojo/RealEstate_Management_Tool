<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        Schema::defaultStringLength(191);
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');

        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $relativeUrl = route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false);

            $appUrl = config('app.url');
            $resetUrl = $appUrl
                ? rtrim($appUrl, '/') . $relativeUrl
                : url($relativeUrl);

            $passwordConfig = config('auth.passwords.' . config('auth.defaults.passwords'));
            $expiresIn = $passwordConfig['expire'] ?? 60;

            return (new MailMessage)
                ->subject('Reset your Orange Real Estate password')
                ->greeting('Hello ' . $notifiable->name . ',')
                ->line('We received a request to reset the password for your Orange Real Estate account.')
                ->action('Create new password', $resetUrl)
                ->line("This secure link expires in {$expiresIn} minutes.")
                ->line('If you did not request a password reset, you can safely ignore this email.');
        });
    }
}
