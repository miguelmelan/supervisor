<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UiPathAutomationCloudController extends Controller
{
    public function redirect()
    {
        $redirectUrl = Socialite::driver('uipath')->redirect()->getTargetUrl();
        return response('', 409)->header('X-Inertia-Location', $redirectUrl);
    }

    public function callback()
    {
        $user = Socialite::driver('uipath')->user();
        $existingUser = User::where('uipath_id', $user->id)->first();

        if ($existingUser) {
            Auth::login($existingUser);
            return redirect()->intended();
        } else {
            $newUser = User::updateOrCreate(['email' => $user->email], [
                'name' => $user->name,
                'uipath_id' => $user->id,
                'password' => encrypt('123456dummy')
            ]);
            $newUser->markEmailAsVerified();

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
