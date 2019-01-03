@extends('layouts.layout_guest')

@section('pagelevelstyles')
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection

@section('innercontent')

@include('flash')

<div class="content-form-page">
    <div class="row">
        
        <div class="col-md-7 col-md-offset-1">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/contact') }}">
                {!! csrf_field() !!}
                <fieldset>
                    <legend style="color: #cf00af"><i class="fa fa-edit"></i> Contact Us</legend>
                    <div class="form-group">
                        <label for="name" class="col-lg-2 control-label">Name</label>
                        <div class="col-lg-10">
                            <?php
                                $readonly = $model->is_auth == 1 ? 'readonly' : '';
                                echo "<input required $readonly maxlength='100' type='text' class='form-control' name='name' value='$model->name'>";
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-lg-2 control-label">E-Mail</label>
                        <div class="col-lg-10">
                            <?= "<input required $readonly maxlength='128' type='email' class='form-control' name='email' value='$model->email'>" ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subject" class="col-lg-2 control-label">Subject</label>
                        <div class="col-lg-10">
                            <input maxlength="150" required type="text" class="form-control" name="subject" value="{{ $model->subject }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="body" class="col-lg-2 control-label">Body</label>
                        <div class="col-lg-10">
                            <textarea required rows="5" maxlength="500" type="text" class="form-control" name="body" value="{{ $model->body }}">{{ $model->body }}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"></label>
                        <div class="col-lg-10">
                            <div class="g-recaptcha" data-sitekey="{{ env('RE_CAP_SITE') }}"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-10 col-md-offset-2 padding-left-0 padding-top-20">
                            <button type="submit" class="btn btn-primary"> <i class="fa fa-btn fa-envelope"></i> Send</button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
        <div class="col-md-4">
            <div style="margin-top: 50px;">
                <p>
                    If you have any question, comment or suggestion, please feel free to share it with us.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
