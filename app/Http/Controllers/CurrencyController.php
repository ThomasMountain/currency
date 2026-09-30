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
        $series = Rate::query()
            ->orderBy('rate_date')
            ->get()
            ->groupBy('conversion')
            ->map(fn ($rates) => [
                'labels' => $rates->pluck('rate_date')->all(),
                'values' => $rates->pluck('rate')->map(fn ($rate) => (float) $rate)->all(),
            ]);

        return view('dashboard', compact('series'));
    }
}
