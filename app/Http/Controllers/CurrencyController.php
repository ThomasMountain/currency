<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    public function index(): View
    {
        // Group by pair so each currency pair becomes its own dataset, and
        // order by date within each so the chart draws a real time series.
        // rate_date is cast to a date, so it is formatted explicitly rather
        // than serialised - otherwise the labels would be ISO datetimes.
        $series = Rate::query()
            ->orderBy('rate_date')
            ->get()
            ->groupBy('conversion')
            ->map(fn ($rates) => [
                'labels' => $rates->map(fn (Rate $rate) => $rate->rate_date->format('Y-m-d'))->all(),
                'values' => $rates->map(fn (Rate $rate) => (float) $rate->rate)->all(),
            ]);

        return view('dashboard', compact('series'));
    }
}
