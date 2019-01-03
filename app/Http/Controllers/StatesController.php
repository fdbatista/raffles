<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VState;

class StatesController extends Controller
{
    public function __construct()
    {
    }
    
    public function getStates(Request $request)
    {
        return VState::where(['country_id' => $request->country_id])->orderBy('name')->get();
    }
}
