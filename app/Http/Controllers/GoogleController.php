<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirect()
    {
        $redirectUrl = Socialite::driver('google')->redirect()->getTargetUrl();
        return response('', 409)->header('X-Inertia-Location', $redirectUrl);
    }

    public function callback()
    {
        $user = Socialite::driver('google')->user();
        $existingUser = User::where('google_id', $user->id)->first();

        if ($existingUser) {
            Auth::login($existingUser);
            return redirect()->intended();
        } else {
            $newUser = User::updateOrCreate(['email' => $user->email], [
                'name' => $user->name,
                'google_id' => $user->id,
                'password' => encrypt('123456dummy')
            ]);

            if (count($newUser->ownedTeams) === 0) {
                $newUser->ownedTeams()->create([
                    'name' => "$user->name's Team",
                    'personal_team' => true,
                ]);
            }
            
            Auth::login($newUser);

            return redirect()->intended();
        }
    }
}
