<?php

namespace App\Http\Controllers;

use App\Models\VCountry;

class CountriesController extends Controller
{
    public function __construct()
    {
    }
    
    public function getCountries()
    {
        return VCountry::all();
    }
}
