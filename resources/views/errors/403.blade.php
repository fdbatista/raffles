@extends('layouts.layout')

@section('content')
    
    <div class="row margin-bottom-40">
        <!-- BEGIN CONTENT -->
        <div class="col-md-12 col-sm-12">
          <div class="content-page page-404">
             <div class="number">
                403
             </div>
             <div class="details">
                <h3>Stop! You cannot go any further from here.</h3>
                <p>
                   You are not allowed to access the requested resource.<br>
                   <a href="{{ url('/') }}" class="link">Return home</a>.
                </p>
             </div>
          </div>
        </div>
        <!-- END CONTENT -->
    </div>

@stop
