@extends('layouts.layout_guest')
@section('pagelevelstyles')

@parent

<link href="{{ asset('assets/plugins/bootstrap-checkbox/css/build.css' )}}" rel="stylesheet">

@stop

@section('innercontent')

<h1>Create an account</h1>
<div class="content-form-page" ng-controller="RegisterController" ng-init="userInit('<?= url('') ?>', '<?= old('country_id') ?>', '<?= old('state_id') ?>', '<?= old('phone_number') ?>')">
    <div class="row">
        <div class="col-md-8 col-sm-8">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                {!! csrf_field() !!}
                <fieldset>
                    <legend>Your personal details</legend>
                    
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name" class="col-lg-4 control-label">First Name</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-edit"></i>
                                </span>
                                <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" maxlength="50" id="name" type="text" class="form-control" name="name" value="{{ old('name') }}">
                            </div>
                            @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                        <label for="last_name" class="col-lg-4 control-label">Last Name</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-bookmark"></i>
                                </span>
                                <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" maxlength="50" id="last_name" type="text" class="form-control" name="last_name" value="{{ old('last_name') }}">
                            </div>
                            @if ($errors->has('last_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('last_name') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                        <label for="username" class="col-lg-4 control-label">Username</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user"></i>
                                </span>
                                <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" maxlength="30" id="username" type="text" class="form-control" name="username" value="{{ old('username') }}">
                            </div>
                            @if ($errors->has('username'))
                            <span class="help-block">
                                <strong>{{ $errors->first('username') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label for="address" class="col-lg-4 control-label">Address</label>
                        <div class="col-lg-8">
                            <textarea id="address" maxlength="250" class="form-control label-text" name="address">{{ old('address') }}</textarea>
                            @if ($errors->has('address'))
                            <span class="help-block">
                                <strong>{{ $errors->first('address') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }}">
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
                            @if ($errors->has('country_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('country_id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group" id="div-loading-sm" style="display: none;">
                        <div class="col-lg-offset-4">
                            <span>Loading states...</span> <img src="{{ asset('/assets/img/loading_sm.gif') }}" />
                        </div>
                    </div>
                    
                    <div class="form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
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
                            @if ($errors->has('state_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('state_id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                        <label for="city" class="col-lg-4 control-label">City</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-map-marker"></i>
                                </span>
                                <input type="text" class="form-control" name="city" id="city" value="{{ old('city') }}" />
                            </div>
                            @if ($errors->has('city'))
                            <span class="help-block">
                                <strong>{{ $errors->first('city') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group{{ $errors->has('zip_code') ? ' has-error' : '' }}">
                        <label for="zip_code" class="col-lg-4 control-label">Zip Code</label>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-envelope"></i>
                                </span>
                                <input type="number" min="0" max="9999999999" class="form-control" id="zip_code" name="zip_code" value="{{ old('zip_code') }}" />
                            </div>
                            @if ($errors->has('zip_code'))
                            <span class="help-block">
                                <strong>{{ $errors->first('zip_code') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group{{ $errors->has('phone_number') ? ' has-error' : '' }}">
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
                                <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" ui-mask="(999) 999-9999" ng-model="phone_number" id="phone_number" type="text" class="form-control" name="phone_number" value="{{ old('phone_number') }}">
                            </div>
                            @if ($errors->has('phone_number'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone_number') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="col-lg-4 control-label">E-Mail Address</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-internet-explorer"></i>
                                </span>
                                <input required oninvalid="this.setCustomValidity('This field requires a valid email address')" onchange="this.setCustomValidity('')" maxlength="75" id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">
                            </div>
                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('paypal') ? ' has-error' : '' }}">
                        <label for="paypal" class="col-lg-4 control-label">PayPal Account</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-paypal"></i>
                                </span>
                                <input required oninvalid="this.setCustomValidity('This field requires a valid PayPal account')" onchange="this.setCustomValidity('')" maxlength="75" id="paypal" type="email" class="form-control" name="paypal" value="{{ old('paypal') }}">
                            </div>
                            @if ($errors->has('paypal'))
                            <span class="help-block">
                                <strong>{{ $errors->first('paypal') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                </fieldset>
                <fieldset>
                    <legend>Your password</legend>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="col-lg-4 control-label">Password</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-lock"></i>
                                </span>
                                <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" id="password" type="password" class="form-control" name="password">
                            </div>
                            @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <label for="password_confirmation" class="col-lg-4 control-label">Confirm Password</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-lock"></i>
                                </span>
                                <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" id="password_confirmation" type="password" class="form-control" name="password_confirmation">
                            </div>
                            @if ($errors->has('password_confirmation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </fieldset>
                <div class="row">
                    
                    <div class="col-lg-8 col-md-offset-4 padding-left-0{{ $errors->has('accept_terms') ? ' has-error' : '' }}">
                        <div class="checkbox checkbox-primary">
                            <input id="accept_terms" name="accept_terms" type="checkbox" class="styled">
                            <label for="accept_terms">
                                I agree with terms and conditions of use.
                            </label>
                            @if ($errors->has('accept_terms'))
                            <span class="help-block">
                                <strong>{{ $errors->first('accept_terms') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-lg-8 col-md-offset-4 padding-left-0 padding-top-20">
                        <button type="submit" class="btn btn-primary"> <i class="fa fa-btn fa-user"></i> Create an account</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-4 col-sm-4 pull-right">
            <div class="form-info">
                <h2><em>Important</em> Information</h2>
                <p>
                    In order to proceed, you must accept our terms and conditions of use.<br /><br />
                    If you need more information, you may read them <a href="#" data-toggle="modal" data-target="#terms-conditions"> here.</a>
                </p>
            </div>
        </div>
        
        <div style="display: none;" id="terms-conditions" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="gridModalLabel">Terms and Conditions</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p>
                                    <?= $termsAndConditions ?>
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
