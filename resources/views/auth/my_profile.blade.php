@extends('layouts.layout_guest')

@section('pagelevelstyles')

@parent

@stop

@section('innercontent')

@include('flash')

@if (count($errors) > 0)
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
        <p><i class="fa fa-info-circle fa-exclamation-triangle"></i> {{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="content-form-page" ng-controller="RegisterController" ng-init="userInit('<?= url('') ?>', '<?= $model->country_id ?>', '<?= $model->state_id ?>', '<?= $model->phone_number ?>')">
    <div class="row">
        
        <div class="col-md-8 col-sm-8">
            
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/update-profile') }}">
                {!! csrf_field() !!}
                <fieldset>
                    <legend>My Profile</legend>
                    <?php
                        $classSubscriptionButton =  $subscription_price > 0 ? "" : "hidden";
                    ?>
                    
                    <div class="form-group <?= $classSubscriptionButton ?>">
                        <div class="col-lg-offset-4 col-lg-8">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirm-subscription"><i class="fa fa-btn fa-euro"></i> Activate my Subscription</button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="name" class="col-lg-4 control-label">First Name</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-edit"></i>
                                </span>
                                <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" maxlength="50" id="name" type="text" class="form-control" name="name" value="{{ $model->name }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name" class="col-lg-4 control-label">Last Name</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-bookmark"></i>
                                </span>
                                <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" maxlength="50" id="last_name" type="text" class="form-control" name="last_name" value="{{ $model->last_name }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="username" class="col-lg-4 control-label">Username</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" maxlength="30" id="username" type="text" class="form-control" name="username" value="{{ $model->username }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address" class="col-lg-4 control-label">Address</label>
                        <div class="col-lg-8">
                            <textarea id="address" maxlength="250" class="form-control label-text" name="address">{{ $model->address }}</textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="country_id" class="col-lg-4 control-label">Country</label>
                        <div class="col-lg-8">
                            <input type="hidden" name="country_id" value="@{{ country.id }}" />
                            <ui-select required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" id="country_id" ng-change="findCountry(country.id)" ng-model="country">
                                <ui-select-match>
                                    <img class="flag" src="/assets/img/flags/@{{ country.name }}.png"/> <span class="label-text" ng-bind="country.name"></span>
                                </ui-select-match>
                                <ui-select-choices repeat="item in (countries | filter: $select.search) track by item.id">
                                    <img width="24" height="auto" class="flag" src="/assets/img/flags/@{{ item.name }}.png"/> <span ng-bind="item.name"></span>
                                </ui-select-choices>
                            </ui-select>
                        </div>
                    </div>
                    
                    <div class="form-group" id="div-loading-sm" style="display: none;">
                        <div class="col-lg-offset-4">
                            <span>Loading states...</span> <img src="{{ asset('/assets/img/loading_sm.gif') }}" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="state_id" class="col-lg-4 control-label">State</label>
                        <div class="col-lg-8">
                            <input type="hidden" name="state_id" ng-model="state.id" value="@{{ state.id }}" />
                            <ui-select required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" id="state_id" ng-change="findState(state.id)" ng-model="state">
                                <ui-select-match>
                                    <span class="label-text" ng-bind="state.name"></span>
                                </ui-select-match>
                                <ui-select-choices repeat="item in (states | filter: $select.search) track by item.id">
                                    <span ng-bind="item.name"></span>
                                </ui-select-choices>
                            </ui-select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="city" class="col-lg-4 control-label">City</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-map-marker"></i>
                                </span>
                                <input type="text" class="form-control" name="city" id="city" value="{{ $model->city }}" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="zip_code" class="col-lg-4 control-label">Zip Code</label>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                <input type="number" min="0" max="9999999999" class="form-control" id="zip_code" name="zip_code" value="{{ $model->zip_code != 0 ? $model->zip_code : null }}" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone_number" class="col-lg-4 control-label">Phone number</label>
                        <div class="col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-globe"></i>
                                </span>
                                <input maxlength="4" ng-model="country.phone_code" id="phone_code" readonly type="text" class="form-control" name="phone_code" value="@{{ country_phone_code }}">
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-phone"></i>
                                </span>
                                <input ui-mask="(999) 999-9999" required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" ng-model="phone_number" id="phone_number" type="text" class="form-control" name="phone_number" value="{{ $model->phone_number }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="col-lg-4 control-label">E-Mail Address</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-internet-explorer"></i>
                                </span>
                                <input required oninvalid="this.setCustomValidity('This field requires a valid email address')" onchange="this.setCustomValidity('')" maxlength="75" id="email" type="email" class="form-control" name="email" value="{{ $model->email }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="paypal" class="col-lg-4 control-label">PayPal Account</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-paypal"></i>
                                </span>
                                <input required oninvalid="this.setCustomValidity('This field requires a valid PayPal account')" onchange="this.setCustomValidity('')" maxlength="75" id="paypal" type="email" class="form-control" name="paypal" value="{{ $model->paypal }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-8 col-md-offset-4 padding-left-0 padding-top-20">
                            <button type="submit" class="btn btn-primary"> <i class="fa fa-btn fa-user"></i> Update profile</button>
                        </div>
                    </div>
                    
                </fieldset>
            </form>
            
            <div class="row">
                <div class="col-lg-8 col-md-offset-4 padding-left-0 padding-top-20">
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirm-cancel_account"><i class="fa fa-btn fa-trash"></i> Delete my User Account</button>
                </div>
            </div>
            
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/change-password') }}">
                {!! csrf_field() !!}
                <fieldset>
                    <legend>My password</legend>
                    
                    <div class="form-group">
                        <label for="old_password" class="col-lg-4 control-label">Old Password</label>
                        <div class="col-lg-8">
                            <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" id="old_password" type="password" class="form-control" name="old_password" value="">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password" class="col-lg-4 control-label">New Password</label>
                        <div class="col-lg-8">
                            <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" id="new_password" type="password" class="form-control" name="new_password" value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation" class="col-lg-4 control-label">Confirm Password</label>
                        <div class="col-lg-8">
                            <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" id="new_password_confirmation" type="password" class="form-control" name="new_password_confirmation" value="">
                        </div>
                    </div>
                </fieldset>
                
                <div class="row">
                    <div class="col-lg-8 col-md-offset-4 padding-left-0 padding-top-20">                        
                        <button type="submit" class="btn btn-primary"> <i class="fa fa-btn fa-lock"></i> Change password</button>
                    </div>
                </div>
            </form>
            
        </div>
        
        <div style="display: none;" id="confirm-subscription" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    
                    <form action="<?= $paypal_url ?>" method="post">
                        {!! csrf_field() !!}
                        
                        <input type="hidden" name="cmd" value="_xclick">
                        <input type="hidden" name="cancel_return" value="<?= url('/product-list') ?>">
                        <input type="hidden" name="return" value="<?= url('payment/process-subscription-results') ?>">
                        <input type="hidden" name="business" value="<?= $sys_paypal_account ?>">
                        <input type="hidden" name="lc" value="C2">
                        <input type="hidden" id="item_name" name="item_name" value="User Subscription">
                        <input type="hidden" id="item_number" name="item_number" value="<?= $model->id ?>">
                        <input type="hidden" name="amount" value="<?= $subscription_price ?>" id="total-amount">
                        <input type="hidden" name="currency_code" value="USD">
                        <input type="hidden" name="button_subtype" value="services">
                        <input type="hidden" name="no_note" value="0">
                        
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="gridModalLabel">Confirm payment</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p>
                                        You are about to be redirected to PayPal's site in order to confirm your payment.<br />
                                        Subscription price is <code><?= $subscription_price ?> USD</code>.<br /><br />
                                        <b>Do you want to continue?</b>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" class="btn btn-primary btn-lg" value="Yes" />
                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
        
        <div style="display: none;" id="confirm-cancel_account" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/cancel-account') }}">
                        {!! csrf_field() !!}
                        
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <p style="font-size: 1.5em;">
                                <i class="fa fa-exclamation-triangle"></i>
                                Caution!
                            </p>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p>
                                        You are about to cancel your user account on this site.
                                        This means that you won't be able to login anymore, and
                                        all your statistics and personal data will be lost!<br /><br />
                                        <b>If you wish to continue, type in your password
                                        and press the button "Delete my Account"</b>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <input id="old_password" type="password" class="form-control" name="old_password" value="">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary"> <i class="fa fa-btn fa-trash"></i> Delete my Account</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>

@endsection

@section('javascripts')
@parent

<script src='{{ asset("app/controllers/register_controller.js") }}'></script>

<script>
    $(document).ready(function() {
        App.init();
        ComponentsDropdowns.init();
    });
</script>
@endsection
