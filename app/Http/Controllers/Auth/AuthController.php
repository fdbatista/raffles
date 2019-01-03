<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Auth;
use App\Http\Controllers\StaticMembersController;
use App\Models\AppConfig;
use App\Models\VNextRaffle;
use App\Models\VUserActiveRaffle;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    use RegistersUsers, ThrottlesLogins;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'myProfile', 'updateProfile', 'changePassword', 'cancelAccount']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:50',
            'username' => 'required|max:30|unique:users',
            'last_name' => 'required|max:50',
            'address' => 'max:250',
            'phone_number' => 'required|max:20|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'paypal' => 'required|email|max:128|unique:users',
            'password' => 'required|confirmed|min:6',
            'accept_terms' => 'required',
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'city' => 'max:75',
            'zip_code' => 'integer|max:9999999999',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'last_name' => $data['last_name'],
            'address' => $data['address'],
            'phone_number' => $data['phone_number'],
            'email' => $data['email'],
            'paypal' => $data['paypal'],
            'password' => bcrypt($data['password']),
            'country_id' => $data['country_id'],
            'state_id' => $data['state_id'],
            'city' => $data['city'],
            'zip_code' => $data['zip_code'] != 0 ? $data['zip_code'] : null,
            'remember_token' => str_random(100),
            'activation_token' => str_random(100),
            'api_token' => str_random(100),
            'status_id' => 2,
        ]);
    }
    
    protected function logout(Request $request)
    {
        $this->changeApiToken();
        StaticMembersController::unsetCommonSessionMessages($request);
        Auth::logout();
        return view('home');
    }
    
    private function changeApiToken()
    {
        $user = Auth::user();
        if ($user)
        {
            $user->api_token = str_random(100);
            $user->save();
        }
    }
    
    public function showLoginForm(Request $request)
    {
        $raffle_id = $request->input('raffle_id');
        $appConfig = AppConfig::find(1);
        return view('auth.login', ['app_title' => $appConfig->app_title, 'raffle_id' => $raffle_id]);
    }
    
    public function login(Request $request)
    {
        StaticMembersController::unsetCommonSessionMessages($request);
        $email = $request->email;
        $password = $request->password;
        $raffle_id = $request->raffle_id;
        
        if (Auth::attempt(['username' => $email, 'password' => $password, 'status_id' => 1]))
        {
            $this->changeApiToken();
            //$res = Auth::onceBasic();
            if ($raffle_id)
                return redirect()->intended(url("/product-details/$raffle_id"));
            return redirect()->intended('/');
        }
        
        if (Auth::attempt(['email' => $email, 'password' => $password, 'status_id' => 1]))
        {
            $this->changeApiToken();
            //$res = Auth::onceBasic();
            if ($raffle_id)
                return redirect()->intended(url("/product-details/$raffle_id"));
            return redirect()->intended('/');
        }
        
        $user = User::findByUsernameOrEmail($email);
        
        if ($user)
        {
            if (Hash::check($password, $user->password))
            {
                if ($user->activation_token)
                {
                    StaticMembersController::setSessionMessage($request, 'warning', 'You need to activate your account before login.');
                }
                elseif ($user->status_id == 2)
                {
                    StaticMembersController::setSessionMessage($request, 'warning', 'Your account has been blocked for security reasons.');
                }
                else
                {
                    StaticMembersController::setSessionMessage($request, 'error', 'Invalid username or password.');
                }
            }
            else
            {
                StaticMembersController::setSessionMessage($request, 'error', 'Invalid username or password.');
            }
        }
        else
        {
            StaticMembersController::setSessionMessage($request, 'error', 'Invalid username or password.');
        }
        return $this->showLoginForm($request);
    }
    
    public static function getMessage()
    {
        $res = session('message');
        return $res;
    }
    
    public function confirmEmail(Request $request, $token)
    {
        if ($token)
        {
            $user = User::findByActivationToken($token);
            if ($user)
            {
                $user->confirmEmail();
                StaticMembersController::setSessionMessage($request, 'message', 'Your account has been confirmed. You may now log in with your username and password.');
            }
        }
        return redirect('login');
    }
    
    public function passwordResetForm()
    {
        return view('auth.passwords.email');
    }
    
    public function myProfile(Request $request)
    {
        if (Auth::check())
        {
            $model = Auth::user();
            $appConfig = AppConfig::find(1);
            return view('auth.my_profile', ['model' => $model, 'subscription_price' => $model->subscribed == 1 ? 0 : $appConfig->subscription_price, 'sys_paypal_account' => $appConfig->paypal, 'paypal_url' => str_contains($appConfig->paypal_url, 'http') ? $appConfig->paypal_url : url($appConfig->paypal_url), 'validationErrors' => []]);
        }
		return $this->showLoginForm($request);
    }
    
    public function updateProfile(Request $request)
    {
        $updatedUser = Auth::user();
        
        $this->validate($request, [
            'name' => 'required|max:50',
            'username' => 'required|max:30|unique:users,username,' . $updatedUser->id,
            'last_name' => 'required|max:50',
            'address' => 'max:250',
            'email' => 'required|email|max:255|unique:users,email,' . $updatedUser->id,
            'paypal' => 'required|email|max:128|unique:users,paypal,' . $updatedUser->id,
            'phone_number' => 'required|max:20|unique:users,phone_number,' . $updatedUser->id,
            'country_id' => 'required',
            'state_id' => 'required',
            'city' => 'max:75',
            'zip_code' => 'integer|max:9999999999',
        ]);
        
        $updatedUser->name = $request->name;
        $updatedUser->username = $request->username;
        $updatedUser->last_name = $request->last_name;
        $updatedUser->address = $request->address;
        $updatedUser->phone_number = $request->phone_number;
        $updatedUser->email = $request->email;
        $updatedUser->paypal = $request->paypal;
        $updatedUser->country_id = $request->country_id;
        $updatedUser->state_id = $request->state_id;
        $updatedUser->city = $request->city;
        $updatedUser->zip_code = $request->zip_code;
        $updatedUser->api_token = str_random(100);
        $updatedUser->save();
        StaticMembersController::setSessionMessage($request, 'message', 'Your profile has been succesfully updated.');
        $appConfig = AppConfig::find(1);
        return view('auth.my_profile', ['model' => $updatedUser, 'subscription_price' => $updatedUser->subscribed == 1 ? 0 : $appConfig->subscription_price, 'sys_paypal_account' => $appConfig->paypal, 'paypal_url' => str_contains($appConfig->paypal_url, 'http') ? $appConfig->paypal_url : url($appConfig->paypal_url), 'validationErrors' => []]);
    }
    
    public function changePassword(Request $request)
    {
        StaticMembersController::unsetSessionMessages($request, ['message', 'warning', 'error', 'errors']);
        $updatedUser = Auth::user();
        
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required|confirmed|different:old_password',
            'new_password_confirmation' => 'required',
        ]);
        
        if (Hash::check($request->old_password, $updatedUser->password))
        {
            $updatedUser->password = bcrypt($request->new_password);
            $updatedUser->api_token = str_random(100);
            $updatedUser->save();
            StaticMembersController::setSessionMessage($request, 'message', 'Your password has been succesfully updated.');
        }
        else
        {
            StaticMembersController::setSessionMessage($request, 'error', 'Your old password does not match.');
        }
        $appConfig = AppConfig::find(1);
        return view('auth.my_profile', ['model' => $updatedUser, 'subscription_price' => $updatedUser->subscribed == 1 ? 0 : $appConfig->subscription_price, 'sys_paypal_account' => $appConfig->paypal, 'paypal_url' => str_contains($appConfig->paypal_url, 'http') ? $appConfig->paypal_url : url($appConfig->paypal_url), 'validationErrors' => []]);
    }
    
    public function cancelAccount(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'old_password' => 'required',
        ]);
        if (Hash::check($request->old_password, $user->password))
        {
            $pendingRaffles = VNextRaffle::where(['user_id' => $user->id])->get();
            if (count($pendingRaffles) > 0)
            {
                StaticMembersController::setSessionMessage($request, 'error', 'Your user account will not be deleted as long as you have pending raffles.');
            }
            else
            {
                $boughtTickets = VUserActiveRaffle::where(['user_id' => $user->id])->get();
                if (count($boughtTickets) > 0)
                {
                    StaticMembersController::setSessionMessage($request, 'error', 'Your user account will not be deleted as long as you have tickets from pending raffles.');
                }
                else
                {
                    $isAdmin = $user->isAdministrator();
                    $adminsLeft = DB::select('select 1 from `users` where `role_id` = 1 and `status_id` = 1 and `id` <> ? limit 1', [$user->id]);
                    if ($isAdmin && !$adminsLeft)
                    {
                         StaticMembersController::setSessionMessage($request, 'error', 'Your user account cannot be deleted because you are the only active site administrator.');
                    }
                    else
                    {
                        $res = DB::delete('delete from `users` where `id` = ?', [$user->id]);
                        if ($res == '1')
                        {
                            Auth::logout();
                            StaticMembersController::setSessionMessage($request, 'message', 'Your user account has been deleted.');
                            $appConfig = AppConfig::find(1);
                            return view('auth.login', ['app_title' => $appConfig->app_title]);
                        }  
                    }
                }
            }
        }
        else
        {
            StaticMembersController::setSessionMessage($request, 'error', 'Your old password does not match.');
        }
        $appConfig = AppConfig::find(1);
        return view('auth.my_profile', ['model' => $user, 'subscription_price' => $user->subscribed == 1 ? 0 : $appConfig->subscription_price, 'sys_paypal_account' => $appConfig->paypal, 'paypal_url' => str_contains($appConfig->paypal_url, 'http') ? $appConfig->paypal_url : url($appConfig->paypal_url), 'validationErrors' => []]);
    }
    
}
