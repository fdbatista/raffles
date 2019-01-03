@extends('layouts.layout')

@section('content')
    
    <div class="row margin-bottom-40">
        <!-- BEGIN CONTENT -->
        <div class="col-md-12 col-sm-12">
          <div class="content-page page-404">
             <div class="number">
                404
             </div>
             <div class="details">
                <h3>Oops!  You're lost.</h3>
                <p>
                   We can not find the resource you're looking for.<br>
                   <a href="{{ url('/') }}" class="link">Return home</a>.
                </p>
             </div>
          </div>
        </div>
        <!-- END CONTENT -->
    </div>

@stop
