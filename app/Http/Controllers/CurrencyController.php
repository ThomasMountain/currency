<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    public function index(): View
    {
        $rates = Rate::all();

        return view('dashboard', compact('rates'));
    }
}
