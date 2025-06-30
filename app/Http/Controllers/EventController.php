<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EventRegistrationRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\EventResource;
use App\Models\TicketTypes;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Spatie\Image\Image;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\QueryBuilder;


class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $event = QueryBuilder::for(Event::class)
            ->where('user_id', Auth::user()->id)
            ->allowedFilters(['date'])
            ->allowedSorts('name')
            ->orderBy('created_at', 'desc')
            ->paginate(6)
            ->appends(request()->query());

        return Inertia::render('Event/Index', [
            'events' => EventResource::collection($event)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Event/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRegistrationRequest $request)
    {
        $data = $request->validated();

        $data['available_ticket'] = $data['total_ticket'];

        if (isset($data['image'])) {
            $file = $data['image'];
            $path = $file->store('event_images', 'public');
            $data['image'] = $path;

            $fullPath = Storage::disk('public')->path($path);
            Image::load($fullPath)
                ->width(1200)
                ->height(600)
                ->quality(100) // Add watermark if needed
                ->optimize()
                ->save();

            if (isset($data['ticket_info'])) {
                $totalTicket = array_reduce($data['ticket_info'], function ($carry, $item) {
                    return $carry + $item['ticket_count'];
                }, 0);

                $data['total_ticket'] = $totalTicket;
                $data['available_ticket'] = $data['total_ticket'];
            }

            $event = Event::create($data);

            if (isset($data['ticket_info'])) {
                foreach ($data['ticket_info'] as $ticketType) {
                    $ticketType['event_id'] = $event->id;
                    $this->storeTicketTypes($ticketType);
                }
            }

            return redirect()->route('event.index')->with('message', 'event created successfully');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::with(['ticketTypes'])->findOrFail($id);
        return Inertia::render('Event/Show', [
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'image' => Storage::url($event->image),
                'date' => $event->date,
                'registration_expires_at' => $event->registration_expires_at,
                'ticket_types' => $event->ticketTypes ? $event->ticketTypes : null
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $event = Event::with(['ticketTypes'])->findOrFail($id);
        return Inertia::render('Event/Edit', [
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'image' => Storage::url($event->image),
                'date' => $event->date,
                'registration_expires_at' => $event->registration_expires_at,
                'ticket_types' => $event->ticketTypes ? $event->ticketTypes : null
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        Gate::authorize('update', $event);

        $data = $request->validated();


        if (isset($data['image']) && !is_null($data['image'])) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $file = $data['image'];
            $path = $file->store('event_images', 'public');
            $data['image'] = $path;

            $fullPath = Storage::disk('public')->path($path);
            Image::load($fullPath)
                ->width(1200)
                ->height(600)
                ->quality(100)
                ->optimize()
                ->save();
        } else {
            unset($data['image']);
        }

        if (isset($data['ticket_info'])) {
            $totalTicket = array_reduce($data['ticket_info'], function ($carry, $item) {
                return $carry + $item['ticket_count'];
            }, 0);

            $data['total_ticket'] = $totalTicket;
            $data['available_ticket'] = $totalTicket;
        }


        $event->update($data);

        if (isset($data['ticket_info'])) {
            $existingIds = $event->ticketTypes()->pluck('id')->toArray();
            $newIds = Arr::pluck($data['ticket_info'], 'id');
            $toDelete = array_diff($existingIds, $newIds);
            TicketTypes::destroy($toDelete);

            $ticketMap = collect($data['ticket_info'])->keyBy('id');
            foreach ($event->ticketTypes as $ticketType) {
                if (isset($ticketMap[$ticketType->id])) {
                    $this->updateTicketTypes($ticketType, $ticketMap[$ticketType->id]);
                }
            }

            foreach ($data['ticket_info'] as $ticketType) {
                if (!isset($ticketType['id'])) {
                    $ticketType['event_id'] = $event->id;
                    $this->storeTicketTypes($ticketType);
                }
            }
        }

        return redirect()->route('event.index')->with('message', 'event updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        Gate::authorize('delete', $event);

        $event->delete();
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        return redirect()->route('event.index')->with('message', 'event deleted successfully');
    }

    private function storeTicketTypes(array $data)
    {
        $data['ticket_available'] = $data['ticket_count'];

        $validator = Validator::make($data, [
            'event_id' => 'exists:App\Models\Event,id',
            'ticket_type' => 'required|string',
            'ticket_count' => 'required|int',
            'ticket_available' => 'present',
            'ticket_price' => 'nullable|int'
        ]);

        // dd($validator);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        TicketTypes::create($validator->validated());
    }

    private function updateTicketTypes(TicketTypes $ticketTypes, array $data)
    {

        $data['ticket_available'] = $data['ticket_count'];


        $validator = Validator::make($data, [
            'id' => 'exists:App\Models\TicketTypes,id',
            'ticket_type' => 'required|string',
            'ticket_count' => 'required|int',
            'ticket_available' => 'present',
            'ticket_price' => 'nullable'
        ]);

        //  dd($data);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $ticketTypes->update($validator->validated());
    }
}
