@extends('layouts.layout_guest')

@section('pagelevelstyles')
@parent


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
<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-12">
        <a class="breadcrumb" href="<?= url('/users') ?>">Users</a> /
        <a class="breadcrumb" href="<?= url("/users/details/$model->id") ?>">{{ $model->username }}</a>
    </div>
</div>
<div class="content-form-page">
    <div class="row">
        <div class="col-md-7 col-sm-7">
            <form class="form-horizontal" role="form">
                <fieldset>
                    <legend style="color: #cf00af;"><i class="fa fa-user"></i> User Details</legend>
                    
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Username</label>
                        <div class="col-lg-8">
                            <input readonly type="text" class="form-control" name="paypal" value="{{ $model->username }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">First Name</label>
                        <div class="col-lg-8">
                            <input readonly type="text" class="form-control" name="paypal" value="{{ $model->name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Last Name</label>
                        <div class="col-lg-8">
                            <input readonly type="text" class="form-control" name="paypal" value="{{ $model->last_name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">User Role</label>
                        <div class="col-lg-8">
                            <input readonly type="text" class="form-control" name="paypal" value="{{ $model->role }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">User Status</label>
                        <div class="col-lg-8">
                            <input readonly type="text" class="form-control" name="paypal" value="{{ $model->status }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Registration Date</label>
                        <div class="col-lg-8">
                            <input readonly type="text" class="form-control" name="paypal" value="{{ $model->created_at }}">
                        </div>
                    </div>
                    <!--<div class="form-group">
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
                    </div>-->
                </fieldset>
            </form>
        </div>
    </div>
</div>

@endsection

@section('javascripts')
@parent
@endsection
