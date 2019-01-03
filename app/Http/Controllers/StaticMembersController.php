<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\VEmailNotifications;
use App\Mailers\AppMailer;
use App\Models\AppConfig;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class StaticMembersController extends Controller
{
    
    public static function parseActionName($route)
    {
        return substr($route, strrpos($route, '@') + 1);
    }
    
    public static function setSessionMessage(Request $request, $type, $message)
    {
        $request->session()->flash($type, $message);
    }
    
    public static function unsetSessionMessages(Request $request, $types)
    {
        $request->session()->forget($types);
    }
    
    public static function unsetCommonSessionMessages(Request $request)
    {
        $request->session()->forget(['message', 'error', 'warning']);
    }

    public static function sendEmailConfirmation($user)
    {
        try
        {
            $mailer = new AppMailer();
            $res = $mailer->sendEmailConfirmation($user);
            return $res;
        }
        catch (\Exception $exc)
        {
            return $exc->getMessage();
        }
    }
    
    public static function sendContactEmail($model)
    {
        try
        {
            $mailer = new AppMailer();
            $res = $mailer->sendContactEmail($model);
            return $res;
        }
        catch (\Exception $exc)
        {
            return $exc->getMessage();
        }
    }
    
    public static function sendPasswordResetLink($user, $token)
    {
        try
        {
            $mailer = new AppMailer();
            $res = $mailer->sendPasswordResetLink($user, $token);
            return $res;
        }
        catch (\Exception $exc)
        {
            return null;
        }
    }

    public static function sendTransactionConfirmation($model)
    {
        try
        {
            $mailer = new AppMailer();
            $res = $mailer->sendTransactionConfirmation($model);
            return $res;
        }
        catch (\Exception $exc)
        {
            return null;
        }
    }
    
    
    public static function sendRaffleExecutionAlert($notification)
    {
        try
        {
            $mailer = new AppMailer();
            $res = $mailer->sendRaffleExecutionAlert($notification);
            return $res;
        }
        catch (\Exception $exc)
        {
            return null;
        }
    }
    
    public static function executeRafflesJob()
    {
        DB::statement('call `execute_raffles`');
        $notifications = VEmailNotifications::get();
        $sentNotificationsIds = '';
        foreach ($notifications as $notif)
        {
            $res = StaticMembersController::sendRaffleExecutionAlert($notif);
            if ($res)
            {
                $sentNotificationsIds .= "$notif->id,";
            }
        }
        
        if ($sentNotificationsIds)
        {
            $sentNotificationsIds = trim($sentNotificationsIds, ",");
            DB::update("update `users_notifications` set `via_email` = 0 where `id` in ($sentNotificationsIds)");
        }
    }
    
    public static function executeRefundsJob()
    {
        $transactions = Transaction::where(['transaction_status_id' => 3])->get();
        $paymentController = new PaymentController();
        foreach ($transactions as $transaction)
        {
            $refundRes = $paymentController->makeRefund($transaction->paypal_transaction_id, $transaction->amount_to_refund);
            if (!str_contains($refundRes, "An error has occurred"))
            {
                $transaction->transaction_status_id = 4;
                $transaction->save();
            }
        }
    }
    
    public static function getPayPalURL()
    {
        $config = AppConfig::find(1);
        return str_contains($config->paypal_url, 'http') ? $config->paypal_url : url($config->paypal_url);
    }
    
    public static function perCent($value, $percent)
    {
        return round($value * $percent / 100, 2);
    }
    
    public static function removeNonIntegerChars($string)
    {
        $pattern = ['(', ')', '-', ' '];
        $replace = ['', '', '', ''];
        return str_replace($pattern, $replace, $string);
    }
}
