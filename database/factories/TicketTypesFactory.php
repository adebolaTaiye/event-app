<?php

namespace Database\Factories;

use App\Models\TicketTypes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketTypes>
 */
class TicketTypesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TicketTypes::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ticketCount = $this->faker->numberBetween(10, 20);
        $options = ['VIP','regular'];

        return [
            'event_id' => $this->faker->numberBetween(10, 20),
            'ticket_type' => $this->faker->randomElement($options),
            'ticket_count' => $ticketCount,
            'ticket_available' => $ticketCount,
        ];
    }

}
