<?php

namespace App\Services;

use App\Models\Rate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyRateService
{
    protected string $baseUrl = 'https://free.currencyconverterapi.com/api/v6/convert';

    public function getRateForDate(string $currencyPair, Carbon $date): ?float
    {
        $response = Http::get($this->baseUrl, [
            'q' => $currencyPair,
            'compact' => 'ultra',
            'date' => $date->format('Y-m-d'),
            'endDate' => $date->format('Y-m-d'),
            'apiKey' => config('services.currency_api.key'),
        ]);

        if ($response->failed()) {
            Log::error("Failed to fetch rate for {$currencyPair} on {$date->format('Y-m-d')}: ".$response->body());

            return null;
        }

        $ratesData = $response->json();

        return $ratesData[$currencyPair][$date->format('Y-m-d')] ?? null;
    }

    public function storeRate(string $currency, $rate): void
    {
        if (! Rate::where('rate_date', $rate['date'])->where('conversion', $currency)->exists()) {
            Rate::create([
                'conversion' => $currency,
                'rate_date' => $rate['date'],
                'rate' => $rate['rate'],
            ]);
        }
    }
}
