<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    public function create()
    {
       return view('auth.login');
    }

    public function store(User $user, Request $request){
        $attributes = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required'],
        ]);

        if(!Auth::attempt($attributes)){
            return back()->withErrors([
                'password' => 'We are unable to authenticate using the provided credentials.',
            ])
                ->withInput();
        }

        $request->session()->regenerate();
        return redirect()->intended('/')->with('success', 'You have been logged in!');
    }

    public function destroy(){
        Auth::logout();

        return redirect('/')->with('success', 'You have been logged out!');
    }
}
