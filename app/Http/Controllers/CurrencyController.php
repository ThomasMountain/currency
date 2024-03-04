<?php

namespace App\Http\Controllers;

use App\Models\Rate;

class CurrencyController extends Controller
{
    public function index()
    {

        $rates = Rate::all();

        return view('dashboard', compact('rates'));
    }
}
