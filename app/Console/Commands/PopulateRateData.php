<?php

namespace App\Console\Commands;

use App\Services\CurrencyRateService;
use Illuminate\Console\Command;

class PopulateRateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:rates {--days=30 : Number of days to backfill}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates the rate data for previous dates';

    /**
     * The currency pairs to track, as "FROM_TO".
     *
     * @var list<string>
     */
    protected array $pairs = ['GBP_EUR', 'EUR_GBP'];

    private CurrencyRateService $currencyRateService;

    public function __construct(CurrencyRateService $currencyRateService)
    {
        parent::__construct();
        $this->currencyRateService = $currencyRateService;
    }

    public function handle()
    {
        $days = max(1, (int) $this->option('days'));

        // The ECB publishes with roughly a day of lag, so today is rarely
        // available yet. Backfill up to and including yesterday.
        $end = now()->subDay();
        $start = $end->copy()->subDays($days - 1);

        $total = 0;

        foreach ($this->pairs as $pair) {
            [$from, $to] = explode('_', $pair);

            $rates = $this->currencyRateService->getRatesForRange($from, $to, $start, $end);

            if ($rates === []) {
                $this->warn("No rates returned for {$pair}.");

                continue;
            }

            $stored = $this->currencyRateService->storeRates($pair, $rates);

            $total += $stored;

            $this->info("{$pair}: {$stored} new rate(s) out of ".count($rates).' returned.');
        }

        $this->info("Rate data populated successfully. {$total} new row(s) in total.");

        return Command::SUCCESS;
    }
}
