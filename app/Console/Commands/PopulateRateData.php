<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Rate;

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
     * @return mixed
     */
    public function handle()
    {
        //Use the API to get JSON data
        $prevDate = now()->subDays(7);
        $base = 'https://free.currencyconverterapi.com/api/v6/convert';
        $query = '?q=GBP_EUR&compact=ultra&date='. $prevDate->format("Y-m-d") .'&endDate=' . now()->format("Y-m-d");

        $dates = json_decode(file_get_contents($base . $query));
        
        foreach($dates->GBP_EUR as $date => $rate){

            //Check whether rate already exists

            if(!Rate::where('rate_date', $date)->exists()){
                Rate::create([
                    'rate_date' => $date,
                    'rate' => $rate
                ]);
            }
            
        }
        


    }
}