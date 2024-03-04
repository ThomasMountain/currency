<?php

namespace App\Console\Commands;

use App\Models\Rate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PopulateRateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates the rate data for previous dates';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $prevDate = now()->subDays(7);
        $base = 'https://free.currencyconverterapi.com/api/v6/convert';

        $currencies = [
            'GBP_EUR',
            'EUR_GBP',
        ];

        foreach ($currencies as $currency) {
            $arguments = [
                'q' => $currency,
                'compact' => 'ultra',
                'date' => $prevDate->format('Y-m-d'),
                'endDate' => now()->format('Y-m-d'),
                'apiKey' => config('services.currency_api.key')
            ];

            $response = Http::get($base, $arguments);

            $dates = $response->json();
            foreach ($dates[$currency] as $date => $rate) {
                if (! Rate::where('rate_date', $date)->exists()) {
                    Rate::create([
                        'conversion' => $currency,
                        'rate_date' => $date,
                        'rate' => $rate,
                    ]);
                }
            }
        }

    }
}
