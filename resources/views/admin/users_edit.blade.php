@extends('layouts.layout_guest')

@section('pagelevelstyles')

@parent

@stop

@section('innercontent')

@include('flash')

<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-12">
        <a class="breadcrumb" href="<?= url('/users') ?>">Users</a> /
        <a class="breadcrumb" href="<?= url("/users/details/$model->id") ?>">{{ $model->username }}</a>
    </div>
</div>

<div class="content-form-page" ng-controller="RegisterController" ng-init="userInit('<?= url('') ?>', '<?= $model->country_id ?>', '<?= $model->state_id ?>', '<?= $model->phone_number ?>')">
    <div class="row">
        <div class="col-md-8 col-sm-8">
            <form class="form-horizontal" role="form" method="POST" action="<?= url("/users/edit") ?>">
                {!! csrf_field() !!}
                <fieldset>
                    <legend style="color: #cf00af;"><i class="fa fa-user"></i> Edit User</legend>
                    <input name="id" type="hidden" value="<?= $model->id ?>" />
                    
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
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">User Role</label>
                        <div class="col-lg-5">
                            <select class="form-control label-text" name="role_id">
                            <?php
                                foreach ($roles as $role) {
                                    $selected = $role->id == $model->role_id ? ' selected ' : '';
                                    echo "<option $selected value='$role->id'>$role->name</option>";
                                }
                            ?>    
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">User Status</label>
                        <div class="col-lg-5">
                            <select class="form-control label-text" name="status_id">
                            <?php
                                foreach ($user_statuses as $status) {
                                    $selected = $status->id == $model->status_id ? ' selected ' : '';
                                    echo "<option $selected value='$status->id'>$status->name</option>";
                                }
                            ?>    
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subscribed" class="col-lg-4 control-label">Subscribed</label>
                        <div class="col-lg-3">
                            <select class="form-control label-text" name="subscribed" id="subscribed">
                                <?php
                                    if ($model->subscribed == '1')
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

<script src='{{ asset("app/controllers/register_controller.js") }}'></script>

<script>
    $(document).ready(function() {
        App.init();
        ComponentsDropdowns.init();
    });
</script>
@endsection