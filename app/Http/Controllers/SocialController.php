<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    //

    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(){

        $userFromGoogle = Socialite::driver('google')->stateless()->user();
        $userFromDatabase = User::where('google_id', $userFromGoogle->getId())->first();

        if (!$userFromDatabase) {
            $newUser = new User([
                'google_id' => $userFromGoogle->getId(),
                'name' => $userFromGoogle->getName(),
                'email' => $userFromGoogle->getEmail()
            ]);
            $newUser->save();

            auth('web')->login($newUser);
            session()->regenerate();

            return redirect('/');
        }

        auth('web')->login($userFromDatabase);
        session()->regenerate();
        redirect('/');
    }

    public function logout(Request $request)
    {
        auth('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
