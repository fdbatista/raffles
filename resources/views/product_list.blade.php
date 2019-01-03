@extends('layouts.layout_guest')

@section('layerslider')

    

@stop

@section('incommingraffles')

   
    
@stop

@section('sidebar')

    @include('partials.categories_sidebar_menu') 
    @include('partials.incommingraffles_sidebar') 

@stop

@section('innercontent')

    @include('partials.product_list')

@stop

