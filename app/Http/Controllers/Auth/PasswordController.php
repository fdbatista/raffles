<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Models\PasswordReset;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    //use ResetsPasswords;

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    public function showResetForm(Request $request, $token = null)
    {
        $request->session()->forget(['message', 'error', 'warning']);
        if (is_null($token)) {
            return $this->getEmail();
        }

        $email = $request->input('email');

        if (property_exists($this, 'resetView')) {
            return view($this->resetView)->with(compact('token', 'email'));
        }

        if (view()->exists('auth.passwords.reset')) {
            return view('auth.passwords.reset')->with(compact('token', 'email'));
        }

        return view('auth.reset')->with(compact('token', 'email'));
    }
    
    public function getEmail()
    {
        return $this->showLinkRequestForm();
    }
    
    public function showLinkRequestForm()
    {
        if (property_exists($this, 'linkRequestView')) {
            return view($this->linkRequestView);
        }

        if (view()->exists('auth.passwords.email')) {
            return view('auth.passwords.email');
        }

        return view('auth.password');
    }
    
    public function sendResetLinkEmail(Request $request)
    {
        $request->session()->forget(['message', 'error', 'warning']);
        $this->validate($request, ['email' => 'required|email']);
        
        $email = $request->email;
        $user = \App\User::findByEmail($email);
        if ($user)
        {
            $resetObj = PasswordReset::all()->where('email', $user->email)->first();
            if (!$resetObj)
                $resetObj = new PasswordReset();
            $resetObj->email = $user->email;
            $resetObj->token = str_random(100);
            $resetObj->save();
            if (\App\Http\Controllers\StaticMembersController::sendPasswordResetLink($user, $resetObj->token))
            {
                $request->session()->put('message', 'We have sent you an email with instructions to reset your password.');
            }
            else
            {
                $request->session()->put('error', 'The email could not be sent. Please, try again in a few minutes.');
            }
        }
        else
        {
            $request->session()->put('error', 'You must enter a valid email.');
        }
        return view('auth.passwords.email');
    }
    
    public function reset(Request $request)
    {
        $this->validate($request, $this->getResetValidationRules());

        /*$credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );*/
        
        $token = $request->token;
        $email = $request->email;
        $newPassword = $request->password;
        
        $user = \App\User::findByEmail($email);
        if ($user)
        {
            $resetObj = PasswordReset::all()->where('email', $email)->first();
            if (!$resetObj)
            {
                $request->session()->put('error', 'This account has not requested a password reset.');
            }
            elseif($resetObj->token != $token)
            {
                $request->session()->put('error', 'This account does not correspond to the current request.');
            }
            else
            {
                $user->password = bcrypt($newPassword);
                $user->save();
                $resetObj->delete();
                $request->session()->put('message', 'Your password has been updated.');
                return view('auth.login');
            }
        }
        else
        {
            $request->session()->put('error', 'You must enter a valid email.');
        }
        return redirect("/password/reset/$token");
    }
    
    protected function getResetValidationRules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
    }
}
