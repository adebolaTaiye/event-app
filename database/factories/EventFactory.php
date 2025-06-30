<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use App\Models\TicketTypes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
   /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'registration_expires_at' => $this->faker->dateTimeBetween('now', '+1 year'),
            'date' => $this->faker->dateTimeBetween('+1 day', '+2 years'),
           // 'location' => $this->faker->address,
            'image' => $this->faker->imageUrl(640, 480, 'public/event_images', true),
            'user_id' => $user->id // Reference to UserFactory
        ];
    }
     /**
     * Configure the factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Event $event) {
            $ticketTypes = TicketTypes::factory()->count(2)->create(['event_id' => $event->id]);

            $totalTicket = $ticketTypes->sum('ticket_count');
            $event->update([
                'total_ticket' => $totalTicket,
                'available_ticket' => $totalTicket,
            ]);
        });
    }
}
