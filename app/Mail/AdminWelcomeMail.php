<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $plainPassword,
        public ?array $friendlyPrivileges = null,
    ) {
    }

    public function build(): self
    {
        $loginUrl = config('app.url') ? rtrim(config('app.url'), '/') . '/login' : route('login');

        return $this
            ->subject('Your Orange Real Estate account is ready')
            ->view('emails.admin_welcome')
            ->with([
                'user' => $this->user,
                'password' => $this->plainPassword,
                'friendlyPrivileges' => $this->friendlyPrivileges,
                'loginUrl' => $loginUrl,
            ]);
    }
}
