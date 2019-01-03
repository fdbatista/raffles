@extends('layouts.layout_guest')

@section('pagelevelstyles')
@parent
@endsection

@section('innercontent')

<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-12">
        <a class="breadcrumb" href="<?= url('/countries') ?>">Countries</a> /
        <a class="breadcrumb"><?= $new_item == 1 ? 'New' : 'Edit'?> Country</a>
    </div>
</div>

<h1></h1>
<div class="content-form-page">
    <div class="row">
        <div class="col-md-8 col-sm-8">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/countries/store') }}" enctype="multipart/form-data">
                {!! csrf_field() !!}
                <fieldset>
                    <legend></legend>
                    
                    <input type="hidden" name="new_item" value="<?= $new_item ?>" />
                    
                    <input type="hidden" name="item_id" value="<?= $model->id ?>" />
                    
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name" class="col-lg-4 control-label">Name</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-globe"></i>
                                </span>
                                <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" maxlength="50" id="name" type="text" class="form-control" name="name" value="{{ $model->name }}">
                            </div>
                            @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group{{ $errors->has('phone_code') ? ' has-error' : '' }}">
                        <label for="phone_code" class="col-lg-4 control-label">Phone Code</label>
                        <div class="col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-phone"></i>
                                </span>
                                <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" maxlength="50" id="phone_code" type="text" class="form-control" name="phone_code" value="{{ $model->phone_code }}">
                            </div>
                            @if ($errors->has('phone_code'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone_code') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group{{ $errors->has('flag_path') ? ' has-error' : '' }}">
                        <label for="flag_path" class="col-lg-4 control-label">Flag</label>
                        <div class="col-lg-8">
                            <input type="file" id="flag_path" name="flag_path" accept=".png">
                            <img width="24" height="24" src="{{ url($model->flag_path != '' ? $model->flag_path : '/assets/img/flags/Other.png') }}" id="img-flag" />
                            @if ($errors->has('flag_path'))
                            <span class="help-block">
                                <strong>{{ $errors->first('flag_path') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                
                </fieldset>
                
                <div class="row">
                    <div class="col-lg-8 col-md-offset-4 padding-left-0 padding-top-20">
                        <button type="submit" class="btn btn-primary"> <i class="fa fa-btn fa-check"></i> Accept</button>
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
<script src='{{ asset("assets/plugins/components_dropdowns/plugins/bootstrap/js/bootstrap2-typeahead.min.js") }}'></script>
<script src='{{ asset("assets/plugins/components_dropdowns/plugins/select2/select2.min.js") }}'></script>
<script src='{{ asset("assets/plugins/components_dropdowns/scripts/app.js") }}'></script>
<script src='{{ asset("assets/plugins/components_dropdowns/scripts/components-dropdowns.js") }}'></script>

<script>
    $(document).ready(function() {
        App.init();
        ComponentsDropdowns.init();
    });
</script>
@endsection
