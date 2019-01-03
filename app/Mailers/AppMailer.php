<?php

namespace App\Mailers;

use App\User;
use App\Models\VEmailNotifications;
use App\Models\ContactForm;
use App\Models\TransactionMailNotification;
use App\Models\AppConfig;
use Illuminate\Support\Facades\Mail;

class AppMailer
{
    protected $subject;
    protected $from;
    protected $to;
    protected $view;
    protected $data = [];
    
    public function __construct()
    {
        $appConfig = AppConfig::find(1);
        $this->from = $appConfig->system_email;
    }

    public function sendEmailConfirmation(User $user)
    {
        $this->to = $user->email;
        $this->subject = 'Confirm your user account on Raffles';
        $this->view = 'emails.confirm';
        $this->data = compact('user');
        return $this->deliver();
    }
    
    public function sendContactEmail(ContactForm $model)
    {
        $this->to = $this->from;
        $this->subject = $model->subject;
        $this->view = 'emails.contact';
        $this->data = compact('model');
        return $this->deliver();
    }
    
    public function sendPasswordResetLink(User $user, $token)
    {
        $this->to = $user->email;
        $this->subject = 'Password reset link on Raffles';
        $this->view = 'emails.password_reset';
        $this->data = compact(['user', 'token']);
        return $this->deliver();
    }

    public function sendTransactionConfirmation(TransactionMailNotification $model)
    {
        $this->to = $model->email;
        $this->subject = 'Raffle Transaction Notification';
        $this->view = 'emails.confirm_transaction';
        $this->data = compact(['model']);
        return $this->deliver();
    }
    
    public function sendRaffleExecutionAlert(VEmailNotifications $notification)
    {
        $this->to = $notification->user_email;
        $this->subject = $notification->name;
        $this->view = 'emails.raffle_notification';
        $this->data = compact(['notification']);
        return $this->deliver();
    }
    
    public function deliver()
    {
        return Mail::send($this->view, $this->data, function ($message) {
            $message->from($this->from, 'Raffles Mail Subsystem')->subject($this->subject)
            ->to($this->to);
        });
    }
}
