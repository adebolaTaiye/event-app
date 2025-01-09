<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EventRegistrationRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Models\TicketTypes;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
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
        ->where('user_id',Auth::user()->id)
        ->allowedFilters(['date'])
        ->allowedSorts('name')
        ->orderBy('updated_at','desc')
        ->paginate(6)
        ->appends(request()->query());

       // $event = Event::where('user_id',Auth::user()->id)->orderBy('updated_at','desc')->paginate(6)->withQueryString();
        return Inertia::render('Event/Index',[
            'events' => $event
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

        if(isset($data['image'])){
            $file = $data['image'];

            $path = $file->store('event_images','public');

            $data['image'] = $path;
        }

        if(isset($data['ticket_info'])){
                $totalTicket = array_reduce($data['ticket_info'],function($carry,$item) {
                    return $carry + $item['ticket_count'];
                },0);

                $data['total_ticket'] = $totalTicket;
                $data['available_ticket'] = $data['total_ticket'];
        }

        $event = Event::create($data);

        if(isset($data['ticket_info'])){
            foreach ($data['ticket_info'] as $ticketType) {
              //  dd($ticketType);
                $ticketType['event_id'] = $event->id;
                $this->storeTicketTypes($ticketType);
            }
        }

        return redirect()->route('dashboard')->with('message','event created successfully');
    }

    private function storeTicketTypes($data)
    {
        $data['ticket_available'] = $data['ticket_count'];

        $validator = Validator::make($data, [
            'event_id' => 'exists:App\Models\Event,id',
            'ticket_type' => 'required|string',
            'ticket_count' => 'required|int',
            'ticket_available' => 'present'
        ]);

       TicketTypes::create($validator->validated());

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::with(['ticketTypes'])->findOrFail($id);
        return Inertia::render('Event/Show',[
            'event' => $event
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        return Inertia::render('Event/Create',[
            'event' => $event->with(['ticketTypes'])->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request)
    {
        $data = $request->validated();

        $data['available_ticket'] = $data['total_ticket'];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        //
    }

}
