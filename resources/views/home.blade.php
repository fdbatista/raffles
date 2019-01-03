@extends('layouts.layout_guest')

@section('layerslider')

    @include('partials.slider')

@stop

@section('incommingraffles')

    @include('partials.incommingraffles')
    
@stop

@section('sidebar')

    @include('partials.categories_sidebar_menu') 

@stop

@section('innercontent')

    @include('partials.three_product_promo')

@stop

