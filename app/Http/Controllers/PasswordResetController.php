<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    /**
     * Send a password reset link to the given email.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success(
                [],
                'If an account exists for that email, we have sent a password reset link.'
            );
        }

        // For security, we don't reveal whether the email exists
        if ($status === Password::INVALID_USER) {
            return $this->success(
                [],
                'If an account exists for that email, we have sent a password reset link.'
            );
        }

        if ($status === Password::RESET_THROTTLED) {
            return response()->json([
                'message' => 'Please wait before requesting another reset link.',
            ], 429);
        }

        return response()->json(['message' => 'Unable to send reset link.'], 500);
    }

    /**
     * Reset the user's password using the token from the email.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $status = Password::reset(
            $request->only('email', 'token', 'password', 'password_confirmation'),
            function ($user, $password) {
                $user->forceFill(['password' => $password])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->success(
                [],
                'Your password has been reset. You can now sign in with your new password.'
            );
        }

        if ($status === Password::INVALID_TOKEN) {
            return response()->json([
                'message' => 'This password reset link is invalid or has expired. Please request a new one.',
            ], 400);
        }

        if ($status === Password::INVALID_USER) {
            return response()->json([
                'message' => 'We could not find a user with that email address.',
            ], 400);
        }

        return response()->json(['message' => 'Unable to reset password.'], 500);
    }
}
