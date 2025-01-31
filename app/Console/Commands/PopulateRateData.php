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
    protected $signature = 'get:rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates the rate data for previous dates';

    private CurrencyRateService $currencyRateService;

    public function __construct(CurrencyRateService $currencyRateService)
    {
        parent::__construct();
        $this->currencyRateService = $currencyRateService;
    }

    public function handle()
    {
        foreach (['GBP_EUR', 'EUR_GBP'] as $currencyPair) {

            $data = $this->currencyRateService->getRateForDate($currencyPair, now());

            $this->currencyRateService->storeRate($currencyPair, $data);
            $this->info('Rate data populated successfully.');

            return Command::SUCCESS;
        }
    }
}
