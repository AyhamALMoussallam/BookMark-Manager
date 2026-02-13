<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class GoogleAuthController extends Controller
{
    /**
     * Redirect user to Google OAuth page
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();

            // 1. Try to find user by google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            // 2. If not found, try by email
            if (!$user) {
                $user = User::where('email', $googleUser->getEmail())->first();
            }

            // 3. If still not found, create new user
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(24)),
                ]);
            }

            // 4. If user exists but google_id missing, link it
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return redirect()->to(
                config('app.frontend_url') . '/auth/callback?token=' . $token
            );

        } catch (\Exception $e) {
            return redirect()->to(
                config('app.frontend_url') . '/login?error=google_auth_failed'
            );
        }
    }

}
