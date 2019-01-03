@extends('layouts.layout')

@section('pagelevelstyles')
<link href="{{ asset('assets/plugins/fancybox/source/jquery.fancybox.css')}}" rel="stylesheet">
<link href="{{ asset('assets/plugins/bxslider/jquery.bxslider.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/plugins/layerslider/css/layerslider.css')}}" type="text/css">
<link href="{{ asset('assets/css/pages/coming-soon.css" rel="stylesheet" type="text/css')}}"/>

@stop

@section('content')

@yield('layerslider')

<div class="main" ng-app="RafflesApp">
    <div class="container">

        <!-- BEGIN INCOMINGRAFFLES-->
        @yield('incommingraffles')
        <!-- END INCOMINGRAFFLES-->

        <!-- BEGIN SIDEBAR & CONTENT -->
        <div class="row margin-bottom-40 ">
            <!-- BEGIN SIDEBAR -->
            <div class="sidebar col-md-3 col-sm-4">
                @yield('sidebar')
            </div>
            <!-- END SIDEBAR -->
            <!-- BEGIN CONTENT -->
            <div class="col-md-9 col-sm-8">
                @yield('innercontent')
            </div>
            <!-- END SIDEBAR -->
        </div>
        <!-- END SIDEBAR & CONTENT -->        

        <!-- BEGIN TWO PRODUCTS & PROMO -->
        <div class="row margin-bottom-35 ">
            <!-- BEGIN TWO PRODUCTS -->
            <div class="col-md-6 two-items-bottom-items">
                @yield('twoproducts')
            </div>
            <!-- END TWO PRODUCTS -->

            <!-- BEGIN PROMO -->
            <div class="col-md-6">
                @yield('promo')
            </div>
            <!-- END PROMO -->
        </div>
    </div>
</div>

@stop

@section('javascripts')

<!-- BEGIN LayerSlider -->
<script src="{{ asset('assets/plugins/layerslider/jQuery/jquery-easing-1.3.js')}}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/layerslider/jQuery/jquery-transit-modified.js')}}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/layerslider/js/layerslider.transitions.js')}}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/layerslider/js/layerslider.kreaturamedia.jquery.js')}}" type="text/javascript"></script>
<!-- END LayerSlider -->


<script src="{{ asset('assets/plugins/backstretch/jquery.backstretch.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('assets/scripts/coming-soon.js')}}" type="text/javascript"></script>

<script type="text/javascript" src="{{ asset('assets/scripts/index.js')}}"></script>

@stop

@section('scripts')

Index.initLayerSlider();
ComingSoon.init();

@stop
