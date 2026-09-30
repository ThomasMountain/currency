<?php

namespace Database\Factories;

use App\Models\Rate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rate>
 */
class RateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Rate>
     */
    protected $model = Rate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'conversion' => 'GBP_EUR',
            'rate_date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'rate' => fake()->randomFloat(6, 0.5, 2.0),
        ];
    }

    /**
     * A rate for the inverse pair, for testing multi-pair charts.
     */
    public function eurGbp(): static
    {
        return $this->state(fn () => [
            'conversion' => 'EUR_GBP',
        ]);
    }
}
