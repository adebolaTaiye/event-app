<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Inertia\Inertia;
use App\Models\UserEventRegistration;
use Illuminate\Support\Number;
use App\Http\Resources\EventResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrganizerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $events = DB::table('events')->select('id')->where('user_id', $user->id);

        $totalEventsCreated = Event::where('user_id', $user->id)->count();

        $totalRevenue = DB::table('user_event_registrations')
            ->whereIn('event_id', $events)
            ->sum('amount');

        $totalRegistrationsCount = DB::table('user_event_registrations')
            ->whereIn('event_id', $events)->count();

        $event = QueryBuilder::for(Event::class)
            ->where('user_id', Auth::user()->id)
            ->allowedFilters(['date'])
            ->allowedSorts('name')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'events' =>  EventResource::collection($event),
            'eventCount' => Number::abbreviate($totalEventsCreated),
            'totalRevenue' => Number::currency($totalRevenue, in: 'NGN'),
            'totalRegistrationsCount' => Number::abbreviate($totalRegistrationsCount)
        ]);
    }
}
