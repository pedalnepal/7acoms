<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;

class CustomerResetPassword extends ResetPassword
{
    /**
     * Build the reset URL against the "user." prefixed route, since
     * CustomerUser resets its password through routes/web.php's
     * user.password.reset route, not the admin's password.reset.
     */
    protected function resetUrl($notifiable)
    {
        return url(route('user.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
