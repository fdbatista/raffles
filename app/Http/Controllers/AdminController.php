<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use App\Models\MainSlider;
use App\Models\VTransactions;
use App\Models\Transaction;
use App\Models\Raffle;
use App\Models\VUser;
use App\Models\RaffleUser;
use App\Models\Role;
use App\Models\UserStatus;
use App\Models\Country;
use App\Models\State;
use App\User;
use Auth;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    private $sliderImagesPath = 'assets/img/main-slider/';

    public function __construct()
    {
        $this->middleware('auth')->except('getSliderItems', 'storeSliderItem', 'updateSliderItem', 'deleteSliderItem', 'getTransactionsLog', 'getUserProducts', 'confirmRefund', 'getUserTickets', 'getUserTicketsNumbers');
        $this->middleware('admin')->except('getSliderItems', 'storeSliderItem', 'updateSliderItem', 'deleteSliderItem', 'getTransactionsLog', 'getUserProducts', 'confirmRefund', 'userDetails', 'getUserTickets', 'getUserTicketsNumbers');
    }
    
    public function appConfig()
    {
        $model = AppConfig::find(1);
        return view('admin.app_config', ['model' => $model]);
    }
    
    public function updateConfig(Request $request)
    {
        $this->validate($request, [
            'paypal' => 'required|email',
            'paypal_url' => 'required',
            'paypal_fee' => 'required|numeric|min:0.1|max:100',
            'mail_server' => 'required',
            'mail_port' => 'required',
            'system_email' => 'required|email',
            'mail_address' => 'required|email',
            'mail_sender_name' => 'required',
            'mail_username' => 'required',
            'mail_password' => 'required',
            'about_us' => 'required',
            'contact_us' => 'required',
            'subscription_price' => 'required',
            'max_upload_filesize' => 'required|min:1|max:99999',
            'app_title' => 'required|max:50',
            'terms_and_conditions' => 'required|max:25500',
        ]);
        $model = AppConfig::find(1);
        $model->paypal = $request->paypal;
        $model->paypal_url = $request->paypal_url;
        $model->paypal_fee = $request->paypal_fee;
        $model->system_email = $request->system_email;
        $model->mail_server = $request->mail_server;
        $model->mail_port = $request->mail_port;
        $model->mail_address = $request->mail_address;
        $model->mail_sender_name = $request->mail_sender_name;
        $model->mail_username = $request->mail_username;
        $model->mail_password = $request->mail_password;
        $model->mail_encryption = $request->mail_encryption;
        $model->about_us = $request->about_us;
        $model->contact_us = $request->contact_us;
        $model->client_id = $request->client_id;
        $model->client_secret = $request->client_secret;
        $model->subscription_price = $request->subscription_price;
        $model->max_upload_filesize = $request->max_upload_filesize;
        $model->app_title = $request->app_title;
        $model->terms_and_conditions = $request->terms_and_conditions;
        $model->allow_raffle_creation = $request->allow_raffle_creation;
        $model->save();
        StaticMembersController::setSessionMessage($request, 'message', 'The application configuration has been succesfully updated.');
        return view('admin.app_config', ['model' => $model]);
    }
    
    public function mainSlider()
    {
        $model = MainSlider::orderBy('id')->get();
        return view('admin.main_slider', ['model' => $model]);
    }
    
    public function getSliderItems(Request $request)
    {
        $token = $request->api_token;
        $user = User::findByApiToken($token);
        $res = [];
        if ($user && $user->role_id == 1)
        {
            $res = MainSlider::orderBy('id')->get();
        }
        return $res;
    }
    
    public function storeSliderItem(Request $request)
    {
        $token = $request->api_token;
        $id = $request->id;
        $image = $request->image;
        $content = $request->content;
        $appConfig = AppConfig::find(1);
        
        $this->validate($request, [
            'api_token' => 'required|string|size:100',
            'id' => 'required',
            'image' => 'required|image|max:' . $appConfig->max_upload_filesize,
        ]);
        
        if ($image == 'undefined')
            return 'Error: you must select an image.';
        if ($id != 0)
            return 'Error: invalid request.';
        
        $user = User::findByApiToken($token);
        if ($user && $user->role_id == 1)
        {
            $sliderItem = new MainSlider();
            $imageFullName = $image->getClientOriginalName();
            $sliderItem->image_path = $this->sliderImagesPath . $imageFullName;
            $sliderItem->content = $content != 'null' ? $content : '';
            $sliderItem->save();
            $image->move($this->sliderImagesPath, $imageFullName);
            return "Slide has been created.";
        }
        return 'Error: you are not allowed to perform this action';
    }
    
    public function updateSliderItem(Request $request)
    {
        $token = $request->api_token;
        $id = $request->id;
        $image = $request->image;
        $content = $request->content;
        $appConfig = AppConfig::find(1);
        
        $this->validate($request, [
            'api_token' => 'required|string|size:100',
            'id' => 'required|integer|min:1',
            'content' => 'required_if:image,"undefined",null,""',
            'image' => 'required_without:content|max:' . $appConfig->max_upload_filesize
        ]);
        
        $user = User::findByApiToken($token);
        if ($user && $user->role_id == 1)
        {
            $sliderItem = MainSlider::find($id);
            if ($image != "undefined")
            {
                $fullImagePath = public_path() . '/' . $sliderItem->image_path;
                if (file_exists($fullImagePath))
                    File::delete($fullImagePath);
                $imageFullName = $image->getClientOriginalName();
                $sliderItem->image_path = $this->sliderImagesPath . $imageFullName;
                $image->move($this->sliderImagesPath, $imageFullName);
            }
            $sliderItem->content = $content;
            $sliderItem->save();
            return "Slide has been updated.";
        }
        return 'Error: you are not allowed to perform this action';
    }
    
    public function deleteSliderItem(Request $request)
    {
        $token = $request->api_token;
        $id = $request->id;
        $user = User::findByApiToken($token);
        if ($id && $user && $user->role_id == 1)
        {
            $sliderItem = MainSlider::find($id);
            if ($sliderItem)
            {
                $fullImagePath = public_path() . '/' . $sliderItem->image_path;
                if (file_exists($fullImagePath))
                    File::delete($fullImagePath);
                $sliderItem->delete();
                return "Slider item has been removed.";
            }
            return 'Error: item not found';
        }
        return 'Error: you are not allowed to perform this action';
    }
    
    public function transactionsLog($product_id)
    {
        $raffle = Raffle::where('product_id', $product_id)->first();
        if ($raffle)
        {
            $user = User::find($raffle->user_id);
            if ($user)
                return view('admin.transactions', ['raffle_id' => $raffle->id, 'user' => $user]);
        }
        return view('errors.404');
    }
    
    public function getTransactionsLog(Request $request)
    {
        try
        {
            $user = User::findByApiToken($request->api_token);
            if ($user && $user->role_id == 1)
                return VTransactions::where(['raffle_id' => $request->raffle_id])->get();
            return [];
        }
        catch (\Exception $exc)
        {
            return $exc->getMessage();
        }
    }
    
    public function confirmRefund(Request $request)
    {
        $api_token = $request->api_token;
        $user = User::findByApiToken($api_token);
        if ($user->isAdministrator())
        {
            $id = $request->transaction_id;
            $transaction = Transaction::find($id);
            if ($transaction)
            {
                $appConfig = AppConfig::find(1);
                $paymentCtrl = new PaymentController();
                //$transaction->amount_to_refund = StaticMembersController::perCent($transaction->amount_to_pay, 100 - $appConfig->paypal_fee);
                $transaction->amount_to_refund = $transaction->amount_to_refund;
                $result = $paymentCtrl->makeRefund($transaction->paypal_transaction_id, $transaction->amount_to_refund);
                $transaction->transaction_status_id = (str_contains($result, 'An error has occurred')) ? 3 : 4;
                $transaction->save();
                if ($transaction->transaction_status_id == 4)
                {
                    $query = 'update `raffles_users` set `user_id` = null where `raffle_id` = ? and `chosen_number` in (' . $transaction->tickets_numbers . ')';
                    DB::update($query, [$transaction->raffle_id]);
                    return 'Transaction has been successfully refunded';
                }
                return 'Error: transaction could not be refunded. Please, try again later';
            }
            return 'Error: there is no transaction with such identifier';
        }
        return 'Error: you are not authorized to execute this action.';
    }
    
    public function usersList(Request $request)
    {
        $itemsPerPage = 10;
        $page = isset($request->page) ? $request->page : 1;
        $min = $itemsPerPage * ($page - 1);
        $criteria = isset($request->criteria) ? strtolower(trim($request->criteria)) : '';
        $orderBy = isset($request->order_by) ? $request->order_by : 'username';
        $orderMode = isset($request->order_mode) ? $request->order_mode : 'asc';
        $query = "select count(*) count from `v_users` where `criteria` like '%$criteria%'";
        $count = DB::select($query);
        $query = "select * from `v_users` where `criteria` like '%$criteria%' order by `$orderBy` $orderMode limit ?, ?";
        $users = DB::select($query, [$min, $itemsPerPage]);
        $pagesArray = array();
        $pagesCount = ceil($count[0]->count / $itemsPerPage);
        for ($i = 1; $i <= $pagesCount; $i ++)
        {
            $pagesArray[] = $i;
        }
        return view('admin.users_list', ['users' => $users, 'pages' => $pagesArray, 'criteria' => $criteria]);
    }
    
    public function userRaffles($id)
    {
        $user = User::find($id);
        if ($user)
            return view('admin.users_raffles', ['user' => $user]);
        return view('errors.404');
    }
    
    public function getUserProducts(Request $request)
    {
        $res = [];
        $api_token = $request->api_token;
        $user_id = $request->user_id;
        $user = User::findByApiToken($api_token);
        if ($user && $user->isAdministrator())
        {
            $res = DB::select('select * from `v_products` where `user_id` = ? and `status_id` <> 0', [$user_id]);
        }
        return $res;
    }
    
    public function userTickets($id)
    {
        $user = User::find($id);
        return view('admin.users_tickets', ['user' => $user]);
    }
    
    public function getUserTickets(Request $request)
    {
        try
        {
            $user = User::findByApiToken($request->api_token);
            $user_id = $request->user_id;
            $res = [];
            if ($user && $user->isAdministrator())
            {
                $query = "select
                    `v`.`raffle_id`,
                    `v`.`product_info`,
                    `v`.`user_id`,
                    `v`.`tickets_bought`,
                    `v`.`amount_payed`,
                    `v`.`status`,
                    `v`.`product_id`,
                    `v`.`winner_id`,
                    `v`.`contact_method_id`,
                    `v`.`tickets_sold_required`,
                    `v`.`ending_date`,
                    `v`.`owner_id`,
                    `v`.`owner`,
                    case `v`.`winner_id` when ? then 1 else 0 end as `i_won`,
                    case
                        when `v`.`winner_id` is not null then
                            case `v`.`winner_id`
                                when ? then concat('<i style=''color: peru'' class=''fa fa-btn fa-star''></i> Your ticket #<code>', `v`.`winner_ticket`, '</code> is the winner!', '<br />You may contact the owner via ', (select `name` from `contact_methods` where `id` = `v`.`contact_method_id`), ': ', case `v`.`contact_method_id` when 1 then concat('<a href=\"mailto:', `v`.`owner_email`, '\">', `v`.`owner_email`, '</a>') else concat('<a>', `v`.`owner_phone`, '</a>') end)
                                else concat('<a href=\"/users/details/', `v`.`winner_id`, '\">', `v`.`winner`, '</a> has won the raffle with ticket #<code>', `v`.`winner_ticket`, '</code>')
                            end
                        else null
                    end as `winner_info`
                    from `v_users_active_raffles` `v`
                    where `v`.`user_id` = ?";
                $res = DB::select($query, [$user_id, $user_id, $user_id]);
            }
            return $res;
        }
        catch (\Exception $exc)
        {
            return [];
        }
    }
    
    public function getUserTicketsNumbers(Request $request)
    {
        try
        {
            $user = User::findByApiToken($request->api_token);
            if ($user && $user->isAdministrator())
            {
                return RaffleUser::where(['raffle_id' => $request->raffle_id, 'user_id' => $request->user_id])->get()->sortBy('chosen_number');
            }
            return [];
        }
        catch (\Exception $exc)
        {
            return [];
        }
    }
    
    public function userDetails($id)
    {
        $user = VUser::find($id);
        if ($user)
            return view('admin.users_details', ['model' => $user]);
        return view('errors.404');
    }
    
    public function userEdit(Request $request, $id)
    {
        if (Auth::user()->id == $id)
            return $this->usersList($request);
        $user = User::find($id);
        if ($user)
        {
            StaticMembersController::unsetCommonSessionMessages($request);
            $roles = Role::orderBy('name')->get();
            $userStatuses = UserStatus::orderBy('name')->get();
            if (Auth::user()->id == $id)
            {
                StaticMembersController::setSessionMessage($request, 'warning', 'Caution! You are attempting to update your own user account. Proceed carefully.');
            }
            return view('admin.users_edit', ['model' => $user, 'roles' => $roles, 'user_statuses' => $userStatuses]);
        }
        return view('errors.404');
    }
    
    public function userUpdate(Request $request)
    {
        $user = User::find($request->id);
        if ($user)
        {
            $this->validate($request, [
                'name' => 'required|max:50',
                'username' => 'required|max:30|unique:users,username,' . $user->id,
                'last_name' => 'required|max:50',
                'address' => 'max:250',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'paypal' => 'required|email|max:128|unique:users,paypal,' . $user->id,
                'phone_number' => 'required|max:20|unique:users,phone_number,' . $user->id,
                'country_id' => 'required',
                'state_id' => 'required',
                'city' => 'max:75',
                'zip_code' => 'integer|max:9999999999',
            ]);
            StaticMembersController::unsetCommonSessionMessages($request);
            $user->username = $request->username;
            $user->name = $request->name;
            $user->last_name = $request->last_name;
            $user->role_id = $request->role_id;
            $user->status_id = $request->status_id;
            $user->address = $request->address;
            $user->phone_number = $request->phone_number;
            $user->email = $request->email;
            $user->paypal = $request->paypal;
            $user->subscribed = $request->subscribed;
            $user->country_id = $request->country_id;
            $user->state_id = $request->state_id;
            $user->city = $request->city;
            $user->zip_code = $request->zip_code;
            $user->api_token = str_random(100);
            $user->save();
            StaticMembersController::setSessionMessage($request, 'message', 'User has been succesfully updated.');
            $roles = Role::orderBy('name')->get();
            $userStatuses = UserStatus::orderBy('name')->get();
            return view('admin.users_edit', ['model' => $user, 'roles' => $roles, 'user_statuses' => $userStatuses]);
        }
        return view('errors.404');
    }
    
    public function countriesList(Request $request)
    {
        $itemsPerPage = 10;
        $page = isset($request->page) ? $request->page : 1;
        $min = $itemsPerPage * ($page - 1);
        $criteria = isset($request->criteria) ? strtolower(trim($request->criteria)) : '';
        $orderBy = isset($request->order_by) ? $request->order_by : 'name';
        $orderMode = isset($request->order_mode) ? $request->order_mode : 'asc';
        $query = "select count(*) count from `v_countries` where `criteria` like '%$criteria%'";
        $count = DB::select($query);
        $query = "select * from `v_countries` where `criteria` like '%$criteria%' order by `$orderBy` $orderMode limit ?, ?";
        $items = DB::select($query, [$min, $itemsPerPage]);
        
        $pagesArray = [];
        $superPagesArray = [];
        $pagesCount = ceil($count[0]->count / $itemsPerPage);
        $superPageIndex = -1;
        $justAddedPage = false;
        for ($i = 1; $i <= $pagesCount; $i ++)
        {
            $justAddedPage = false;
            $pagesArray[] = $i;
            if ($i % 10 == 0)
            {
                $superPagesArray[] = $pagesArray;
                $pagesArray = [];
                $justAddedPage = true;
            }
            if ($i == $page)
            {
                $superPageIndex = $justAddedPage ? count($superPagesArray) - 1 : count($superPagesArray);
            }
        }
        if (count($pagesArray) > 0)
        {
            $superPagesArray[] = $pagesArray;
        }
        $superPageIndex = $superPageIndex > -1 ? $superPageIndex : count($superPagesArray) - 1;
        $currPages = count($superPagesArray) > 0 ? $superPagesArray[$superPageIndex] : [];
        return view('admin.countries.countries_list', ['items' => $items, 'pages' => $currPages, 'criteria' => $criteria, 'pagesCount' => $pagesCount, 'count' => $count[0]->count]);
    }
    
    public function deleteCountry($id, Request $request)
    {
        $item = Country::find($id);
        if ($item)
        {
            try {
                unlink(public_path() . $item->flag_path);
            }
            catch (\Exception $exc) {}
            $item->delete();
            StaticMembersController::setSessionMessage($request, 'message', 'The selected country has been deleted');
        }
        else
        {
            StaticMembersController::setSessionMessage($request, 'error', 'That country does not exist');
        }
        return $this->countriesList($request);
    }
    
    public function newCountry()
    {
        $model = new Country();
        return view('admin.countries.countries_store', ['model' => $model, 'new_item' => 1]);
    }
    
    public function editCountry($id, Request $request)
    {
        $model = Country::find($id);
        if ($model)
            return view('admin.countries.countries_store', ['model' => $model, 'new_item' => 0]);
        StaticMembersController::setSessionMessage($request, 'error', 'That country does not exist');
        return $this->countriesList($request);
    }
    
    public function storeCountry(Request $request)
    {
        $flagsPath = '/assets/img/flags/';
        $newName = trim($request->name);
        
        if ($request->new_item == 0)
        {
            $model = Country::find($request->item_id);
            if (!$model)
            {
                StaticMembersController::setSessionMessage($request, 'error', 'That country does not exist');
                return $this->countriesList($request);
            }
            if ($model->flag_path && $model->name != $newName)
            {
                rename(public_path() . $model->flag_path, public_path() . $flagsPath . $newName . '.png');
            }
        }
        else
        {
            $model = new Country();
        }
        
        $model->name = $newName;
        $model->phone_code = trim($request->phone_code);
        $model->flag_path = $flagsPath . $model->name . '.png';
        $flag = Input::file('flag_path');
        if ($flag)
        {
            $flag->move(public_path() . $flagsPath, $model->name . '.png');
        }
		else
		{
			if (!file_exists(public_path() . $flagsPath . $model->name . '.png'))
				copy(public_path() . $flagsPath . 'Other.png', public_path() . $flagsPath . $model->name . '.png');
		}
        $model->save();
        StaticMembersController::setSessionMessage($request, 'message', ($request->new_item == 0) ? 'The country has been updated' : 'The country has been created');
        return $this->countriesList($request);
    }
    
    public function statesList($country_id, Request $request)
    {
        $country = Country::find($country_id);
        if ($country)
        {
            $itemsPerPage = 10;
            $page = isset($request->page) ? $request->page : 1;
            $min = $itemsPerPage * ($page - 1);
            $criteria = isset($request->criteria) ? strtolower(trim($request->criteria)) : '';
            $orderBy = isset($request->order_by) ? $request->order_by : 'name';
            $orderMode = isset($request->order_mode) ? $request->order_mode : 'asc';
            $query = "select count(*) count from `v_states` where `country_id` = $country_id and `name` like '%$criteria%'";
            $count = DB::select($query);
            $query = "select * from `v_states` where `country_id` = $country_id and `name` like '%$criteria%' order by `$orderBy` $orderMode limit ?, ?";
            $items = DB::select($query, [$min, $itemsPerPage]);

            $pagesArray = [];
            $superPagesArray = [];
            $pagesCount = ceil($count[0]->count / $itemsPerPage);
            $superPageIndex = -1;
            $justAddedPage = false;
            for ($i = 1; $i <= $pagesCount; $i ++)
            {
                $justAddedPage = false;
                $pagesArray[] = $i;
                if ($i % 10 == 0)
                {
                    $superPagesArray[] = $pagesArray;
                    $pagesArray = [];
                    $justAddedPage = true;
                }
                if ($i == $page)
                {
                    $superPageIndex = $justAddedPage ? count($superPagesArray) - 1 : count($superPagesArray);
                }
            }
            if (count($pagesArray) > 0)
            {
                $superPagesArray[] = $pagesArray;
            }
            $superPageIndex = $superPageIndex > -1 ? $superPageIndex : count($superPagesArray) - 1;
            $currPages = count($superPagesArray) > 0 ? $superPagesArray[$superPageIndex] : [];
            return view('admin.states.states_list', ['items' => $items, 'pages' => $currPages, 'criteria' => $criteria, 'pagesCount' => $pagesCount, 'count' => $count[0]->count, 'country' => $country]);
        }
        return $this->countriesList($request);
    }
    
    public function deleteState($id, Request $request)
    {
        $item = State::find($id);
        if ($item)
        {
            $country_id = $item->country_id;
            $item->delete();
            StaticMembersController::setSessionMessage($request, 'message', 'The state has been deleted');
            return $this->statesList($country_id, $request);
        }
        else
        {
            StaticMembersController::setSessionMessage($request, 'error', 'That state does not exist');
            return $this->countriesList($request);
        }
    }
    
    public function newState($country_id, Request $request)
    {
        $country = Country::find($country_id);
        if ($country)
        {
            $model = new State();
            return view('admin.states.states_store', ['model' => $model, 'new_item' => 1, 'country' => $country]);
        }
        return $this->countriesList($request);
    }
    
    public function editState($id, Request $request)
    {
        $model = State::find($id);
        if ($model)
        {
            $country = Country::find($model->country_id);
            return view('admin.states.states_store', ['model' => $model, 'new_item' => 0, 'country' => $country]);
        }
        StaticMembersController::setSessionMessage($request, 'error', 'That state does not exist');
        return $this->countriesList($request);
    }
    
    public function storeState(Request $request)
    {
        $newName = trim($request->name);        
        if ($request->new_item == 0)
        {
            $model = State::find($request->item_id);
            if (!$model)
            {
                StaticMembersController::setSessionMessage($request, 'error', 'The state does not exist');
                return $this->statesList($request->country_id, $request);
            }
        }
        else
        {
            $model = new State();
        }
        
        $model->name = $newName;
        $country = Country::find($request->country_id);
        if ($country)
        {
            $model->country_id = $country->id;
            $model->save();
            StaticMembersController::setSessionMessage($request, 'message', ($request->new_item == 0) ? 'The state has been updated' : 'The state has been created');
            return $this->statesList($country->id, $request);
        }
        StaticMembersController::setSessionMessage($request, 'error', 'The country does not exist');
        return $this->statesList($request->country_id, $request);
    }
}
