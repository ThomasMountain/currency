<?php

namespace App\Http\Controllers;

use App\Rate;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index(){

        $rates = Rate::all();

        return view('dashboard', compact('rates'));
    }
}
