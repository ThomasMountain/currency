<?php

namespace App\Services;

use App\Models\Rate;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class CurrencyRateService
{
    /**
     * The Frankfurter API (European Central Bank reference rates).
     *
     * No API key is required. Note that the ECB only publishes on business
     * days, so any date in a requested range that falls on a weekend or an
     * ECB holiday is simply absent from the response.
     */
    protected string $baseUrl = 'https://api.frankfurter.app';

    /**
     * Fetch every published rate for a currency pair across a date range.
     *
     * A whole range is retrieved in a single request. The response is keyed
     * by the date the rate was actually published, which is not necessarily
     * the date that was asked for.
     *
     * @return array<string, float> Published date (Y-m-d) => rate
     */
    public function getRatesForRange(string $from, string $to, Carbon $start, Carbon $end): array
    {
        try {
            $response = $this->client()->get(
                sprintf('%s/%s..%s', $this->baseUrl, $start->format('Y-m-d'), $end->format('Y-m-d')),
                ['from' => $from, 'to' => $to],
            );
        } catch (Throwable $e) {
            Log::error("Failed to fetch {$from}/{$to} rates for {$start->format('Y-m-d')}..{$end->format('Y-m-d')}: ".$e->getMessage());

            return [];
        }

        if ($response->failed()) {
            Log::error("Failed to fetch {$from}/{$to} rates for {$start->format('Y-m-d')}..{$end->format('Y-m-d')}: ".$response->body());

            return [];
        }

        return collect($response->json('rates', []))
            ->map(fn ($rates) => is_array($rates) ? ($rates[$to] ?? null) : null)
            ->filter(fn ($rate) => $rate !== null)
            ->all();
    }

    /**
     * Store a set of rates for a pair, skipping any date already recorded.
     *
     * @param  array<string, float>  $rates  Published date (Y-m-d) => rate
     * @return int Number of newly created rows
     */
    public function storeRates(string $pair, array $rates): int
    {
        $stored = 0;

        foreach ($rates as $date => $rate) {
            // whereDate rather than an exact match: rate_date is cast to a
            // date, so it is stored with a time component. An exact string
            // comparison would miss those rows and re-insert duplicates.
            // withTrashed() matters too, because a soft-deleted row still
            // occupies this (conversion, rate_date) pair and the global
            // scope would otherwise hide it.
            $exists = Rate::withTrashed()
                ->where('conversion', $pair)
                ->whereDate('rate_date', $date)
                ->exists();

            if ($exists) {
                continue;
            }

            Rate::create([
                'conversion' => $pair,
                'rate_date' => $date,
                'rate' => $rate,
            ]);

            $stored++;
        }

        return $stored;
    }

    /**
     * Retries transient failures only. A 4xx means there is simply no data for
     * the requested window, so retrying is pointless and the failed response is
     * returned rather than thrown, keeping the caller in control.
     */
    protected function client()
    {
        return Http::acceptJson()
            ->timeout(10)
            ->retry(
                3,
                250,
                fn (Throwable $exception) => $exception instanceof ConnectionException
                    || ($exception instanceof RequestException && $exception->response->serverError()),
                throw: false,
            );
    }
}
