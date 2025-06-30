<?php

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Notifications\EventRegisteredFor;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can access the index route', function () {
    // Create a user
    $user = User::factory()->create();

    // Simulate a request to the index route
    $response = $this->actingAs($user)->get('/event');
    // Assert the route can be accessed
    $response->assertStatus(200);
});

test('can access the show route', function () {
    // Create a user
    $user = User::factory()->create();

    // Create an event
    $event = Event::factory()->create(['user_id' => $user->id]);

    // Simulate a request to the show route
    $response = $this->actingAs($user)->get("/event/{$event->id}");

    // Assert the route can be accessed
    $response->assertStatus(200);
});

test('can access the edit route', function () {
    // Create a user
    $user = User::factory()->create();

    // Create an event
    $event = Event::factory()->create(['user_id' => $user->id]);

    // Simulate a request to the edit route
    $response = $this->actingAs($user)->get("/event/{$event->id}/edit");

    // Assert the route can be accessed
    $response->assertStatus(200);
});

// // test('can register an event', function () {
// //     Storage::fake('event_images');
// //     // Create a user
// //     $user = User::factory()->create();
// //    // Storage::fake('event_images');
// //     // Define ticket info
// //     $ticketInfo = [
// //         ['ticket_type' => 'VIP', 'ticket_count' => 50],
// //         ['ticket_type' => 'General', 'ticket_count' => 100],
// //     ];

// //     // Calculate total tickets
// //     $totalTicket = array_reduce($ticketInfo, function ($carry, $item) {
// //         return $carry + $item['ticket_count'];
// //     }, 0);

// // // Simulate a form submission
// // $response = $this->actingAs($user)->post('/event/store', [
// //     'name' => 'Sample Event',
// //     'description' => 'This is a sample event description.',
// //     'registration_expires_at' => now()->addDays(10),
// //     'date' => now()->addDays(12),
// //    // 'location' => 'Sample Location',
// //     'ticket_info' => $ticketInfo,
// //     'total_ticket' => $totalTicket,
// //     'available_ticket' => $totalTicket,
// //     'user_id' => $user->id,
// //     'image' => UploadedFile::fake()->image('sample-image.jpg'),
// // ]);

// // //Assert the event was created
// // $response->assertStatus(302); // Assuming a redirect after successful creation
// //     $this->assertDatabaseHas('events', [
// //      'name' => 'Sample Event',
// //      'description' => 'This is a sample event description.',
// //      'total_ticket' => $totalTicket,
// //      'available_ticket' => $totalTicket,
// //  ]);
// //    // Assert the ticket types were created
// //   foreach ($ticketInfo as $ticket) {
// //      $this->assertDatabaseHas('ticket_types', [
// //          'event_id' => Event::first()->id,
// //          'ticket_type' => $ticket['ticket_type'],
// //          'ticket_count' => $ticket['ticket_count'],
// //          'ticket_available' => $ticket['ticket_count'],
// //      ]);
// //  }

// // });

it('can update an event', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create(['user_id' => $user->id]);

    $updatedData = [
        'name' => 'Updated Event Name',
        'description' => 'Updated event description.',
        'registration_expires_at' => now()->addDays(15),
        'date' => now()->addDays(20),
       // 'location' => 'Updated Location',
        'ticket_info' => [
            ['ticket_type' => 'VIP', 'ticket_count' => 60],
            ['ticket_type' => 'General', 'ticket_count' => 120],
        ],
    ];
    // Calculate total tickets
    $totalTicket = array_reduce($updatedData['ticket_info'], function ($carry, $item) {
        return $carry + $item['ticket_count'];
    }, 0);

    $updatedData['total_ticket'] = $totalTicket;
    $updatedData['available_ticket'] = $totalTicket;

    $response = $this->actingAs($user)->patch(route('event.update', $event), $updatedData);

    $response->assertStatus(302); // Assuming a redirect after successful update
    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'name' => 'Updated Event Name',
        'description' => 'Updated event description.',
        'total_ticket' => $totalTicket,
        'available_ticket' => $totalTicket,
    ]);
    foreach ($updatedData['ticket_info'] as $ticket) {
        $this->assertDatabaseHas('ticket_types', [
            'event_id' => $event->id,
            'ticket_type' => $ticket['ticket_type'],
            'ticket_count' => $ticket['ticket_count'],
            'ticket_available' => $ticket['ticket_count'],
        ]);
    }
});

it('an organizer cannot update another organizer event', function () {
    $user = User::factory()->create();
    $user2 = User::factory()->create();
    $event = Event::factory()->create(['user_id' => $user->id]);

    $updatedData = [
        'name' => 'Updated Event Name',
        'description' => 'Updated event description.',
        'registration_expires_at' => now()->addDays(15),
        'date' => now()->addDays(20),
       // 'location' => 'Updated Location',
        'ticket_info' => [
            ['ticket_type' => 'VIP', 'ticket_count' => 60],
            ['ticket_type' => 'General', 'ticket_count' => 120],
        ],
    ];
    // Calculate total tickets
    $totalTicket = array_reduce($updatedData['ticket_info'], function ($carry, $item) {
        return $carry + $item['ticket_count'];
    }, 0);

    $updatedData['total_ticket'] = $totalTicket;
    $updatedData['available_ticket'] = $totalTicket;

    $response = $this->actingAs($user2)->patch(route('event.update', $event), $updatedData);
    $response->assertStatus(404);
});

// Test to see if an event can be deleted
test('can delete an event', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('event.destroy', $event));

    $response->assertStatus(302); // Assuming a redirect after successful deletion
    $this->assertDatabaseMissing('events', ['id' => $event->id]);
});

test('an organizer cannot delete another organizer event', function() {
    $user = User::factory()->create();
    $user2 = User::factory()->create();
    $event = Event::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user2)->delete(route('event.destroy', $event));

    $response->assertStatus(404); // Assuming a redirect after successful deletion

});

test('a user can register for an event and get tickets', function () {
    Notification::fake();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create(['role' => 'attendee']);
    $event = Event::factory()->create(['user_id' => $user1->id]);
    //$ticket = $event->ticketTypes->first();
   // $ticketId = $ticket->id;
    //dd($ticketId);

    $eventToRegisterFor = ['user_id' => $user2->id,'event_id' => $event->id, ];

    $response = $this->actingAS($user2)->post(route('user.register',$eventToRegisterFor));
   $response->assertStatus(302);

   // $availableTicketCountEvent = $event->total_ticket - 5;
  //  $availableTicketCountTicket = $ticket->ticket_count - 5;


    $this->assertDatabaseHas('user_event_registrations',[
        'event_id' => $event->id,
        'user_id' => $user2->id,
        //'ticket_type_id' => $ticketId,
       // 'quantity' => 5,
    ]);

   //  $this->assertDatabaseHas('tickets',[
   //    'booking_id' => $eventToRegisterFor['id'];
  // ]);

   // $this->assertDatabaseHas('events',[
    //    'id' => $event->id,
   //     'available_ticket' => $availableTicketCountEvent
   // ]);

    //$this->assertDatabaseHas('ticket_types',[
  //      'event_id' => $event->id,
  //      'ticket_type' => $ticket->ticket_type,
  //      'ticket_available' => $availableTicketCountTicket
  //  ]);

//    Notification::assertSentTo(
 //       [$user2], EventRegisteredFor::class
   // );

});
