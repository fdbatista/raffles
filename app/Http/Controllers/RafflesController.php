<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Raffle;
use App\Models\VRaffle;
use App\Models\RaffleResult;
use App\Models\VNextRaffle;
use App\Models\VTransactions;
use App\Models\RaffleUser;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\AppConfig;
use App\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RafflesController extends Controller
{
    
    public function __construct(Request $request)
    {
        $this->middleware('subscribed')->except(['productDetails', 'productList', 'getNextRaffles', 'searchRaffleAvailableNumbers', 'getMyRaffles', 'getMyTickets', 'getMyTicketsNumbers', 'getNextRaffleByProductId', 'registerTicketsRequest', 'getIncommingRaffles']);
    }
    
    public function getById(Request $request)
    {
        return Raffle::find($request->id);
    }
    
    public function getVRaffleByProductId(Request $request)
    {
        try
        {
            return VRaffle::where(['product_id' => $request->product_id])->first();
        }
        catch (\Exception $exc)
        {
            return null;
        }
    }
    
    public function getRaffleByProductId(Request $request)
    {
        try
        {
            $product = Product::find($request->product_id);
            $user = User::findByApiToken($request->api_token);
            if ($product && $user && $product->user_id == $user->id)
            {
                $res = Raffle::where(['product_id' => $request->product_id, 'user_id' => $user->id])->first();
                if ($res)
                {
                    return $res;
                }
                $res = new Raffle();
                $res->user_id = $user->id;
                return $res;
            }
            return null;
        }
        catch (\Exception $exc)
        {
            return null;
        }
    }
    
    public function getResultsByProductId(Request $request)
    {
        try
        {
            $res = RaffleResult::where(['product_id' => $request->product_id])->first();
            return $res;
        }
        catch (\Exception $exc)
        {
            return null;
        }
    }
    
    public function store(Request $request)
    {
        $obj = $this->getRaffleByProductId($request);
        if ($obj)
        {
            $this->validateObject($request);
            $maxtickets = $request->last_number - $request->first_number + 1;
            if ($request->min_start_tickets > $maxtickets)
            {
                return "Error: minimum sold tickets cannot be greater than $maxtickets";
            }
            
            $interval = date_diff(date_create($request->starting_date), date_create($request->ending_date));
            if ($interval->days > 99)
            {
                return 'Error: selected time interval cannot be greater than 99 days';
            }
            $obj->ticket_price = $request->ticket_price;
            $obj->first_number = $request->first_number;
            $obj->last_number = $request->last_number;
            $obj->min_start_tickets = $request->min_start_tickets;
            $obj->starting_date = $request->starting_date;
            $obj->ending_date = $request->ending_date;
            $obj->product_id = $request->product_id;
            $obj->save();
            return "Raffle schedule has been updated.";
        }
        return "Error: you are not allowed to perform this action.";
    }
    
    public function destroy(Request $request)
    {
        try
        {
            $obj = $this->getRaffleByProductId($request);
            if ($obj)
            {
                $obj->delete();
                return "Raffle schedule has been canceled.";
            }
            return "There wasn't any scheduled raffle for this product.";
        }
        catch (\Exception $exc)
        {
            return null;
        }
    }
    
    public function results(Request $request)
    {
        return $this->getResultsByProductId($request->product_id);
    }
    
    public function getNextRaffles(Request $request)
    {
        $category_id = $request->category_id;
        $order_column = $request->order_column;
        $order_mode = $request->order_mode;
        $search_term = strtolower(trim($request->search_term));
        $itemsPerPage = $request->itemsPerPage;
        $min = $itemsPerPage * ($request->page - 1);
        $filters = "";

        if ($category_id)
        {
            $filters = " where `product_category_id` = $category_id";
        }

        if ($search_term)
        {
            $filters .= ($filters == "") ? "where lower(`product_name`) like '%" . strtolower($search_term) . "%'" : " and lower(`product_name`) like '%" . strtolower($search_term) . "%'";
        }
        $query = "select count(*) `total` from `v_next_raffles` $filters";
        $totalCount = DB::select($query);
        $query = "select * from `v_next_raffles` $filters order by `$order_column` $order_mode, `product_name` asc limit ?, ?";
        $res = DB::select($query, [$min, $itemsPerPage]);
        $finalRes = array_merge($totalCount, $res);
        return $finalRes;
    }
    
    public function getIncommingRaffles(Request $request)
    {
        $incRaffles = VNextRaffle::where('time_remaining', '<', 86401)->orderBy('time_remaining')->get();
        if (count($incRaffles) < 4)
            $incRaffles = VNextRaffle::orderBy('time_remaining')->take(12)->get();
        return $incRaffles;
    }
    
    public function getNextRaffleByProductId(Request $request)
    {
        $id = $request->productId;
        if ($id)
        {
            $query = "select * from `v_next_raffles` where `product_id` = ?";
            $raffle = DB::select($query, [$id]);
            if ($raffle)
            {
                return array_merge([0 => ['total' => 1]], $raffle);
            }
        }
        return array_merge([0 => ['total' => 0]], []);
    }
    
    public function searchRaffleAvailableNumbers(Request $request)
    {
        $itemsPerPage = 48;
        $min = $itemsPerPage * ($request->page - 1);
        $rangeStart = $request->rangeStart;
        $rangeEnd = $request->rangeEnd;
        $query = "select count(*) count from `v_raffles_available_numbers` where `raffle_id` = ? and `chosen_number` like '%$request->number%' and `chosen_number` between $rangeStart and $rangeEnd";
        $totalCount = DB::select($query, [$request->raffle_id]);
        $query = "select * from `v_raffles_available_numbers` where `raffle_id` = ? and `chosen_number` like '%$request->number%' and `chosen_number` between $rangeStart and $rangeEnd limit ?, ?";
        $res = DB::select($query, [$request->raffle_id, $min, $itemsPerPage]);
        $finalRes = array_merge($totalCount, $res);
        return $finalRes;
    }
    
    public function registerTicketsRequest(Request $request)
    {
        try
        {
            $token = $request->api_token;
            $user = User::findByApiToken($token);
            if (!$user || $user->status_id != '1')
            {
                throw new AccessDeniedHttpException;
            }
            $raffleInfo = VRaffle::find($request->raffle_id);
            
            $transaction = new Transaction();
            $transaction->amount_to_pay = round($request->numbers_count * $raffleInfo->ticket_price, 2);
            $transaction->tickets_numbers = $request->numbers;
            $transaction->tickets_count = $request->numbers_count;
            $transaction->ticket_price = $raffleInfo->ticket_price;
            $transaction->user_name = $user->username;
            $transaction->user_id = $user->id;
            $transaction->raffle_id = $raffleInfo->id;
            $transaction->raffle_owner_id = $raffleInfo->user_id;
            $transaction->transaction_status_id = 1;
            $transaction->transaction_description = $raffleInfo->product_name;
            DB::delete('delete from `transactions` where `user_id` = ? and `raffle_id` = ? and `transaction_status_id` = 1', [$user->id, $raffleInfo->id]);
            $transaction->save();
            return $transaction->id;
        }
        catch (\Exception $exc)
        {
            return "";
        }
    }
    
    public function assignRaffleTickets(Request $request)
    {
        try
        {
            $token = $request->api_token;
            $raffle_id = $request->raffle_id;
            $numbers = $request->numbers;
            $user = User::findByApiToken($token);
            if (!$user)
            {
                throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
            }
            $res = DB::update("update `raffles_users` set `user_id` = ? where `raffle_id` = ? and `chosen_number` in ($numbers) and `user_id` is null", [$user->id, $raffle_id]);
            return $res;
        }
        catch (\Exception $exc)
        {
            return "";
        }
    }
    
    public function getRaffleDetails(Request $request)
    {
        try
        {
            $user = User::findByApiToken($request->api_token);
            $isAdmin = $user->isAdministrator() ? 1 : 0;
            $res = DB::select('select * from `v_raffles` where `product_id` = ? and (`user_id` = ? or ? = 1)', [$request->id, $user->id, $isAdmin]);
            return json_encode($res[0]);
            //return VRaffle::where(['user_id' => $user->id, 'product_id' => $request->id])->first();
        }
        catch (\Exception $exc)
        {
            return null;
        }
    }
    
    public function myTickets()
    {
        return view('raffles.my_tickets');
    }
    
    public function getMyTickets(Request $request)
    {
        try
        {
            $user = User::findByApiToken($request->api_token);
            //$res = VUserActiveRaffle::where(['user_id' => $user->id])->get();
            //$res = DB::select('select `v`.*, case `v`.`winner_id` when ? then 1 else 0 end as `i_won` from `v_users_active_raffles` `v` where `user_id` = ?', [$user->id, $user->id]);
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
            $res = DB::select($query, [$user->id, $user->id, $user->id]);
            return $res;
        }
        catch (\Exception $exc)
        {
            return null;
        }
    }
    
    public function myTransactions()
    {
        return view('raffles.my_transactions');
    }
    
    public function getMyTransactions(Request $request)
    {
        try
        {
            $user = User::findByApiToken($request->api_token);
            return VTransactions::where(['user_id' => $user->id])->get();
        }
        catch (\Exception $exc)
        {
            return null;
        }
    }
    
    public function getMyTicketsNumbers(Request $request)
    {
        try
        {
            $user = User::findByApiToken($request->api_token);
            return RaffleUser::where(['raffle_id' => $request->raffle_id, 'user_id' => $user->id])->get()->sortBy('chosen_number');
        }
        catch (\Exception $exc)
        {
            return null;
        }
    }
    
    public function productList($id = null)
    {
        $config = AppConfig::find(1);
        $nextRafflesCount = ($id == null) ? VNextRaffle::count() : VNextRaffle::where('product_category_id', $id)->count();
        $paypalUrl = str_contains($config->paypal_url, 'http') ? $config->paypal_url : url($config->paypal_url);
        return view('product_list', ['itemsCount' => $nextRafflesCount, 'category_id' => $id, 'sys_paypal_account' => $config->paypal, 'paypal_url' => $paypalUrl]);
    }
    
    public function productDetails($id)
    {
        $config = AppConfig::find(1);
        $paypalUrl = str_contains($config->paypal_url, 'http') ? $config->paypal_url : url($config->paypal_url);
        return view('product_list', ['itemsCount' => 1, 'product_id' => $id, 'sys_paypal_account' => $config->paypal, 'paypal_url' => $paypalUrl]);
    }
    
    private function validateObject(Request $request)
    {
        $first_number = $request->first_number;
        $min_start_tickets = $request->last_number - $request->first_number + 1;
        $this->validate($request, [
            'first_number' => 'required|min:1|max:99999999999',
            'last_number' => "required|min:$first_number|max:99999999999",
            'min_start_tickets' => "required|min:1|max:$min_start_tickets",
            'starting_date' => 'required|date',
            'ending_date' => 'required|date|after:starting_date'
        ]);
    }
    
}
