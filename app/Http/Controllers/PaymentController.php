<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\StaticMembersController;
use Auth;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Models\AppConfig;
use PayPal\Api\Amount;
use PayPal\Api\Refund;
use PayPal\Api\Sale;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use App\Models\TransactionMailNotification;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function processPayment(Request $request)
    {
        if (Auth::user()->paypal)
        {
            $params = [
                'business' => $request->business,
                'item_name' => $request->item_name,
                'item_number' => $request->item_number,
                'payment_gross' => $request->amount,
                'mc_currency' => $request->currency_code,
                'cancel_return' => $request->cancel_return,
                'return' => $request->return,
                'notify_url' => $request->return,
            ];
            return view('payments.process-payment', ['params' => $params]);
        }
        else
        {
            StaticMembersController::setSessionMessage($request, 'error', 'You need to specify a PayPal account in your user profile.');
            return redirect()->intended('/product-list');
        }
    }
    
    private function getUsefulParameters(Request $request)
    {
        $params = [
            'item_number' => $request->input('item_number'), // Product ID
            'paypal_transaction_id' => $request->input('txn_id'), // Paypal transaction ID
            'amount_payed' => $request->input('payment_gross'), // Paypal received amount value
            'currency' => $request->input('mc_currency'), // Paypal received currency type
            'payment_status' => $request->input('payment_status'), // Paypal payment status
            'product_status' => $request->input('payment_status'), // Paypal payment status
            'final_result' => '',
        ];
        return $params;
    }
    
    public function processPaymentResults(Request $request)
    {
        $params = $this->getUsefulParameters($request);
        
        $transaction = Transaction::find($params['item_number']);
        $transaction->paypal_transaction_id = $params['paypal_transaction_id'];
        $operationDesc = '';
        $appConfig = AppConfig::find(1);
        $percentToRefund = 100 - $appConfig->paypal_fee;
        $transaction->amount_to_refund = StaticMembersController::perCent($params['amount_payed'], $percentToRefund);
        
        if ($params['amount_payed'] < $transaction->amount_to_pay) //The user did not pay the correct amount
        {
            
            $refundRes = $this->makeRefund($transaction->paypal_transaction_id, $transaction->amount_to_refund); //Full refund
            if (str_contains($refundRes, "An error has occurred"))
            {
                $transaction->transaction_status_id = 3;
                $operationDesc = "An attempt to rollback the transaction was made because you did not pay the correct amount.\n$refundRes";
            }
            else
            {
                $transaction->transaction_status_id = 4;
                $operationDesc = "Transaction has been rollbacked because you did not pay the correct amount.\n$refundRes";
            }
        }
        else
        {
            $availableNumbersArray = DB::select("select `chosen_number` from `raffles_users` where `raffle_id` = ? and `chosen_number` in ($transaction->tickets_numbers) and `user_id` is null order by `chosen_number`", [$transaction->raffle_id]);
            $effectiveNumbers = DB::update("update `raffles_users` set `user_id` = ?, `transaction_id` = ? where `raffle_id` = ? and `chosen_number` in ($transaction->tickets_numbers) and `user_id` is null", [$transaction->user_id, $transaction->id, $transaction->raffle_id]);
            $difference = $transaction->tickets_count - $effectiveNumbers; //Difference between the numbers originally selected - the ones succesfully updated
            if ($difference > 0)    //Some of the numbers were already bought
            {
                $avNumbersStr = '';
                foreach ($availableNumbersArray as $number) {
                    $avNumbersStr .= $number->chosen_number . ',';
                }
                $transaction->amount_to_refund = StaticMembersController::perCent($transaction->ticket_price * $difference, $percentToRefund);
                $transaction->tickets_numbers = rtrim($avNumbersStr, ',');
                $refundRes = $this->makeRefund($transaction->paypal_transaction_id, $transaction->amount_to_refund); //Partial refund
                if (str_contains($refundRes, "An error has occurred"))
                {
                    $transaction->transaction_status_id = 3;
                    $operationDesc = "An attempt to refund $$transaction->amount_to_refund was made because some of the numbers you selected were already sold.\n$refundRes";
                }
                else
                {
                    $transaction->transaction_status_id = 2;
                    $operationDesc = "The amount of $$transaction->amount_to_refund has been refunded to your account because some of the numbers yo selected were already sold.";
                }
            }
            elseif ($difference < 0)    //Something wrong here. Difference should always be >= 0. You cannot reserve more numbers than the ones selected by the user
            {
                DB::update("update `raffles_users` set `user_id` = null, `transaction_id` = null where `transaction_id` = ?", [$transaction->id]);
                $refundRes = $this->makeRefund($transaction->paypal_transaction_id, $transaction->amount_to_refund); //Full refund
                if (str_contains($refundRes, "An error has occurred"))
                {
                    $transaction->transaction_status_id = 3;
                    $operationDesc = "An attempt to rollback the transaction was made. You chosed $transaction->tickets_count numbers, but $effectiveNumbers were updated.\n$refundRes";
                }
                else
                {
                    $transaction->transaction_status_id = 4;
                    $operationDesc = "Transaction has been rollbacked because you chosed $transaction->tickets_count numbers, but $effectiveNumbers were updated.\n$refundRes";
                }
                $transaction->save();
            }
            else    //Everything went fine
            {
                $transaction->transaction_status_id = 2;
                $operationDesc = "Transaction has been successful";
                $email = $request->email;
                $email = Auth::user()->email;
                $user = \App\User::findByEmail($email);
                if ($user)
                {
                    $notifModel = new TransactionMailNotification();
                    $notifModel->email = $user->email;
                    $notifModel->username = $user->name;
                    $notifModel->transaction_id = $transaction->paypal_transaction_id;
                    $notifModel->description = $transaction->transaction_description;
                    $notifModel->date = date('l, F jS, Y - h:i:s A');
                    $notifModel->tickets = $transaction->tickets_numbers;
                    $notifModel->amount = $transaction->amount_to_pay;
                    $notifModel->percent_to_refund = $percentToRefund;

                    if (\App\Http\Controllers\StaticMembersController::sendTransactionConfirmation($notifModel))
                    {
                        $request->session()->put('message', 'An email with a transaction\'s details has been sent to you.');
                    }
                    else
                    {
                        $request->session()->put('error', 'The notification email could not be sent.');
                    }
                }
            }
        }
        $transaction->save();
        $params['final_result'] = $operationDesc;
        return view('payments.process-payment-results', ['params' => $params]); 
    }
    
    public function processSubscriptionResults(Request $request)
    {
        $params = $this->getUsefulParameters($request);
        
        $appConfig = AppConfig::find(1);
        $user = Auth::user();
        $transaction = new Transaction();
        $transaction->amount_to_pay = $appConfig->subscription_price;
        $transaction->user_name = $user->username;
        $transaction->user_id = $user->id;
        $transaction->transaction_description = 'User Subscription';
        $transaction->paypal_transaction_id = $params['paypal_transaction_id'];
        $transaction->amount_to_refund = StaticMembersController::perCent($params['amount_payed'], 100 - $appConfig->paypal_fee);
        $operationDesc = '';
        
        if ($params['amount_payed'] < $appConfig->subscription_price)
        {
            $refundRes = $this->makeRefund($transaction->paypal_transaction_id, $transaction->amount_to_refund); //Full refund
            if (str_contains($refundRes, "An error has occurred"))
            {
                $transaction->transaction_status_id = 3;
                $operationDesc = "An attempt to rollback the transaction was made because you payed $" . $params['amount_payed'] . " instead of $" . $appConfig->subscription_price . ".\n$refundRes";
            }
            else
            {
                $transaction->transaction_status_id = 4;
                $operationDesc = "Transaction has been rollbacked because you payed $" . $params['amount_payed'] . " instead of $" . $appConfig->subscription_price . ".\n$refundRes";
            }
        }
        else
        {
            $transaction->transaction_status_id = 2;
            $operationDesc = "Transaction has been successful";
            $user->subscribed = 1;
            $user->save();
        }
        $transaction->save();
        $params['final_result'] = $operationDesc;
        return view('payments.process-payment-results', ['params' => $params]); 
    }
    
    public function makeRefund($saleId, $amount)
    {
        try
        {
            $appConfig = AppConfig::find(1);
            $amt = new Amount();
            $amt->setCurrency('USD')->setTotal($amount);
            $refund = new Refund();
            $refund->setAmount($amt);
            $sale = new Sale();
            $sale->setId($saleId);
            //$apiContext = getApiContext($appConfig->client_id, $appConfig->client_secret);
            $apiContext = $this->getApiContext($appConfig->client_id, $appConfig->client_secret);
            $refundedSale = $sale->refund($refund, $apiContext);
            return $refundedSale->getDescription();
        }
        catch(\Exception $exc)
        {
            return "An error has occurred: " . $exc->getMessage();
        }
    }
    
    private function getApiContext($clientId, $clientSecret)
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $clientId,
                $clientSecret
            )
        );
        $apiContext->setConfig(
            array(
                'mode' => 'sandbox',
                'log.LogEnabled' => true,
                'log.FileName' => '../PayPal.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `FINE` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => true,
                // 'http.CURLOPT_CONNECTTIMEOUT' => 30
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
            )
        );
        return $apiContext;
    }
    
}
