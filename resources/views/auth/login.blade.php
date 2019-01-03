@extends('layouts.layout_guest')

@section('innercontent')
<h1>Login</h1>

@include('flash')

<div class="content-form-page">
    <div class="row">
        <div class="col-md-7 col-sm-7">
            <form class="form-horizontal form-without-legend" role="form" method="POST" action="{{ url('/login') }}">                               
                {!! csrf_field() !!}
                <input type="hidden" name="raffle_id" value="<?= $raffle_id ?>" />
                
                <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="col-lg-4 control-label">Username or Email <span class="require">*</span></label>
                    <div class="col-lg-8">
                        <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" type="text" id="email" class="form-control" name="email" value="{{ old('email') }}">
                        @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                
                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="col-lg-4 control-label">Password <span class="require">*</span></label>
                    <div class="col-lg-8">
                        <input required oninvalid="this.setCustomValidity('This field is required')" onchange="this.setCustomValidity('')" id="password" type="password" class="form-control" name="password">
                        @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8 col-md-offset-4 padding-left-0">
                        <a href="{{ url('/password/reset') }}">Forgot your password?</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-md-offset-4 padding-left-0 padding-top-20">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-md-offset-4 padding-left-0 padding-top-10 padding-right-30">
                        <hr>
                        <div class="login-socio hidden">
                            <p class="text-muted">or login using:</p>
                            <ul class="social-icons">
                                <li><a href="#" data-original-title="facebook" class="facebook" title="facebook"></a></li>
                                <li><a href="#" data-original-title="Twitter" class="twitter" title="Twitter"></a></li>
                                <li><a href="#" data-original-title="Google Plus" class="googleplus" title="Google Plus"></a></li>
                                <li><a href="#" data-original-title="Linkedin" class="linkedin" title="LinkedIn"></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="col-md-4 col-sm-4 pull-left">
            <div class="form-info">
                <p>Welcome to <b><?= $app_title ?>!</b><br /><br />
                    If you are a registered user,<br />
                    use this form to login.<br /><br />
                    Otherwise, you may<br />
                    <a href="<?= url('/register') ?>">create a new account</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
