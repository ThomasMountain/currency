<?php

namespace Tests\Feature;

use App\Models\Rate;
use App\Services\CurrencyRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RateModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_rate_date_is_cast_to_a_date()
    {
        $rate = Rate::factory()->create(['rate_date' => '2024-03-01']);

        $this->assertInstanceOf(Carbon::class, $rate->rate_date);
        $this->assertSame('2024-03-01', $rate->rate_date->format('Y-m-d'));
        $this->assertIsFloat($rate->rate);
    }

    public function test_deleting_a_rate_is_a_soft_delete()
    {
        $rate = Rate::factory()->create();

        $rate->delete();

        $this->assertSoftDeleted($rate);
        $this->assertSame(0, Rate::count());
        $this->assertSame(1, Rate::withTrashed()->count());
    }

    public function test_storing_the_same_rate_twice_is_idempotent()
    {
        $service = app(CurrencyRateService::class);
        $rates = ['2024-03-01' => 1.1704, '2024-03-04' => 1.1685];

        $this->assertSame(2, $service->storeRates('GBP_EUR', $rates));
        $this->assertSame(0, $service->storeRates('GBP_EUR', $rates));
        $this->assertSame(2, Rate::count());
    }

    public function test_a_soft_deleted_rate_is_not_duplicated_on_rebackfill()
    {
        $service = app(CurrencyRateService::class);
        $service->storeRates('GBP_EUR', ['2024-03-01' => 1.1704]);

        Rate::query()->whereDate('rate_date', '2024-03-01')->first()->delete();

        $this->assertSame(0, $service->storeRates('GBP_EUR', ['2024-03-01' => 1.1704]));
        $this->assertSame(1, Rate::withTrashed()->whereDate('rate_date', '2024-03-01')->count());
    }

    public function test_factory_produces_usable_rows()
    {
        $rate = Rate::factory()->create();

        $this->assertSame('GBP_EUR', $rate->conversion);
        $this->assertNotNull($rate->rate_date);
        $this->assertGreaterThan(0, $rate->rate);

        $this->assertSame('EUR_GBP', Rate::factory()->eurGbp()->create()->conversion);
    }
}
