@extends('layouts.layout_guest')

@section('pagelevelstyles')
@parent

<link href="{{ asset('assets/plugins/summernote/dist/summernote.css')}}" rel="stylesheet">

@endsection

@section('innercontent')

@include('flash')

@if (count($errors) > 0)
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
        <p><i class="fa fa-info-circle fa-exclamation-triangle"></i> {{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="content-form-page">
    <div class="row">
        <div class="col-md-7 col-sm-7">
            
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/update-config') }}">
                {!! csrf_field() !!}
                <fieldset>
                    <legend>PayPal Parameters</legend>
                    
                    <div class="form-group">
                        <label for="paypal" class="col-lg-4 control-label">PayPal Account</label>
                        <div class="col-lg-8">
                            <input id="paypal" type="text" class="form-control" name="paypal" value="{{ $model->paypal }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="paypal_url" class="col-lg-4 control-label">PayPal URL</label>
                        <div class="col-lg-8">
                            <input id="paypal_url" type="text" class="form-control" name="paypal_url" value="{{ $model->paypal_url }}">
                            <span style="font-style: italic;"><a href="#" data-toggle="modal" data-target="#paypal-url-examples">Examples</a></span>
                            
                            <div style="display: none;" id="paypal-url-examples" class="modal fade" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                            <h4 class="modal-title" id="gridModalLabel">PayPal URL Examples</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <p>
                                                        <b>PayPal Live:</b> <a>https://www.paypal.com/cgi-bin/webscr</a><br />
                                                        <b>PayPal Sandbox:</b> <a>https://www.sandbox.paypal.com/cgi-bin/webscr</a><br />
                                                        <b>Local Simulation:</b> <a>payment/process-payment</a><br />
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">OK</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="client_id" class="col-lg-4 control-label">Client ID</label>
                        <div class="col-lg-8">
                            <input id="client_id" type="text" class="form-control" name="client_id" value="{{ $model->client_id }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="client_secret" class="col-lg-4 control-label">Client Secret Key</label>
                        <div class="col-lg-8">
                            <input id="client_secret" type="text" class="form-control" name="client_secret" value="{{ $model->client_secret }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="subscription_price" class="col-lg-4 control-label">Subscription's Price</label>
                        <div class="col-lg-3">
                            <input id="subscription_price" type="number" min="1" max="100" class="form-control" name="subscription_price" value="{{ $model->subscription_price }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="paypal_fee" class="col-lg-4 control-label">PayPal Fee (%)</label>
                        <div class="col-lg-3">
                            <input id="paypal_fee" type="number" min="1" max="99" class="form-control" name="paypal_fee" value="{{ $model->paypal_fee }}">
                        </div>
                    </div>
                    
                </fieldset>
                
                <fieldset>
                    <legend>Application Sections</legend>
                    
                    <div class="form-group">
                        <label for="app_title" class="col-lg-4 control-label">Application Title</label>
                        <div class="col-lg-8">
                            <input id="app_title" type="text" class="form-control" name="app_title" value="{{ $model->app_title }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="system_email" class="col-lg-4 control-label">System Email</label>
                        <div class="col-lg-8">
                            <input id="system_email" required type="email" class="form-control" name="system_email" value="{{ $model->system_email }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="about_us" class="col-lg-4 control-label">About Us</label>
                        <div class="col-lg-8">
                            <div style="border: solid 1px #dbdbdb; padding: 5px;">
                                <textarea id="about_us" class="form-control summernote" name="about_us">{{ $model->about_us }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_us" class="col-lg-4 control-label">Contact Us</label>
                        <div class="col-lg-8">
                            <div style="border: solid 1px #dbdbdb; padding: 5px;">
                                <textarea id="contact_us" class="form-control summernote" name="contact_us">{{ $model->contact_us }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="terms_and_conditions" class="col-lg-4 control-label">Terms and Conditions</label>
                        <div class="col-lg-8">
                            <div style="border: solid 1px #dbdbdb; padding: 5px;">
                                <textarea id="terms_and_conditions" class="form-control summernote" name="terms_and_conditions">{{ $model->terms_and_conditions }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                </fieldset>
                
                <fieldset class="hidden">
                    <legend>Mail Configuration</legend>
                    
                    <div class="form-group">
                        <label for="mail_server" class="col-lg-4 control-label">Mail Server</label>
                        <div class="col-lg-8">
                            <input id="mail_server" type="text" class="form-control" name="mail_server" value="{{ $model->mail_server }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="mail_port" class="col-lg-4 control-label">Port</label>
                        <div class="col-lg-3">
                            <input id="mail_port" type="number" class="form-control" name="mail_port" value="{{ $model->mail_port }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="mail_username" class="col-lg-4 control-label">Username</label>
                        <div class="col-lg-8">
                            <input id="mail_username" type="text" class="form-control" name="mail_username" value="{{ $model->mail_username }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="mail_password" class="col-lg-4 control-label">Password</label>
                        <div class="col-lg-8">
                            <input id="mail_password" type="password" class="form-control" name="mail_password" value="{{ $model->mail_password }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="mail_address" class="col-lg-4 control-label">Mail Address</label>
                        <div class="col-lg-8">
                            <input id="mail_address" type="text" class="form-control" name="mail_address" value="{{ $model->mail_address }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="mail_sender_name" class="col-lg-4 control-label">Sender Name</label>
                        <div class="col-lg-8">
                            <input id="mail_sender_name" type="text" class="form-control" name="mail_sender_name" value="{{ $model->mail_sender_name }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="mail_encryption" class="col-lg-4 control-label">Encryption</label>
                        <div class="col-lg-8">
                            <input id="mail_encryption" type="text" class="form-control" name="mail_encryption" value="{{ $model->mail_encryption }}">
                        </div>
                    </div>
                    
                </fieldset>
                
                <fieldset>
                    <legend>Other Configuration</legend>
                    
                    <div class="form-group">
                        <label for="max_upload_filesize" class="col-lg-4 control-label">Uploads Filesize (KB)</label>
                        <div class="col-lg-3">
                            <input id="max_upload_filesize" type="number" min="1" max="99999" class="form-control" name="max_upload_filesize" value="{{ $model->max_upload_filesize }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="max_upload_filesize" class="col-lg-4 control-label">Allow Raffle Creation to Non-Admin Users</label>
                        <div class="col-lg-3">
                            <select class="form-control" name="allow_raffle_creation" id="allow_raffle_creation">
                                <?php
                                    if ($model->allow_raffle_creation == '1')
                                    {?>
                                        <option selected value="1">Yes</option>
                                        <option value="0">No</option>
                                    <?php
                                    }
                                    else
                                    {?>
                                        <option value="1">Yes</option>
                                        <option selected value="0">No</option>
                                    <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <div class="row">
                    <div class="col-lg-8 col-md-offset-4 padding-left-0 padding-top-20">                        
                        <button type="submit" class="btn btn-primary"> <i class="fa fa-btn fa-upload"></i> Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('javascripts')
@parent
<script src='{{ asset("assets/plugins/summernote/dist/summernote.min.js") }}'></script>
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            /*height: 150,
            minHeight: 150,
            maxHeight: 150,
            focus: true,*/
            airMode: true
        });
    });
</script>
@endsection
