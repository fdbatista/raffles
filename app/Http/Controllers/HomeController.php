<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactForm;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    
    public function contact(Request $request)
    {
        $model = new ContactForm();
        if (Auth::check())
        {
            $model->is_auth = 1;
            $user = Auth::user();
            $model->name = $user->name . ' ' . $user->last_name;
            $model->email = $user->email;
        }
        StaticMembersController::unsetCommonSessionMessages($request);
        return view('contact', ['model' => $model]);
    }
    
    public function sendContactEmail(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:100',
            'email' => 'required|email|max:128',
            'subject' => 'required|max:150',
            'body' => 'required|max:500',
        ]);
        
        try
        {
            $secret = env('RE_CAP_SECRET');
            $captchaResponse = $request->input('g-recaptcha-response');
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $captchaResponse);
            $responseData = json_decode($verifyResponse);

            $model = new ContactForm();
            $model->is_auth = Auth::check() ? 1 : 0;
            $model->name = $request->name;
            $model->email = $request->email;
            $model->subject = $request->subject;
            $model->body = $request->body;
            StaticMembersController::unsetCommonSessionMessages($request);
            
            if ($responseData && $responseData->success)
            {
                $res = StaticMembersController::sendContactEmail($model);
                if ($res == 1)
                {
                    $model->subject = '';
                    $model->body = '';
                    StaticMembersController::setSessionMessage($request, 'message', 'Thank you for your support! We will attend your request as soon as posible.');
                }
                else
                {
                    StaticMembersController::setSessionMessage($request, 'error', $res);
                }
            }
            else
            {
                StaticMembersController::setSessionMessage($request, 'error', 'Captcha verification code is incorrect');
            }
            return view('contact', ['model' => $model]);
        }
        catch (\Exception $exc)
        {
            $model = new ContactForm();
            $model->is_auth = Auth::check() ? 1 : 0;
            $model->name = $request->name;
            $model->email = $request->email;
            $model->subject = $request->subject;
            $model->body = $request->body;
            StaticMembersController::setSessionMessage($request, 'error', $exc->getMessage());
            return view('contact', ['model' => $model]);
        }
    }
    
}
