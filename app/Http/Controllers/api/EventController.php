<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $events = Event::all();
        } else {
            $events = Event::where('user_id', $user->id)->get();
        }

        return response()->json($events);
    }

    public function store(Request $request){
        $event= Event::create([
            'user_id'=> $request->user()->id,
            'title'=> $request->title,
            'slug' => \Str::slug($request->title), //slug is the string version of title
            'description'=> $request->description,
            'event_date'=> $request->event_date,
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Successfully created!"
        ]);
    }

    public function update(Request $request, $id){
        $events= Event::findOrFail($id);
        $events->update([
            'user_id'=> 1,
            'title'=> $request->title,
            'slug' => \Str::slug($request->title),
            'description'=> $request->description,
            'event_date'=> $request->event_date,
        ]);

        return response()->json([
            'status' => 200,
            'message' => "Successfully updated!"
        ]);
    }

    public function destroy($id){
        $events= Event::findOrFail($id);
        $events->delete($id);

        return response()->json([
            'status' => 200,
            'message'=> 'Deleted'
        ]);
    }

    public function showPublic($slug)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found'
            ], 404);
        }

        return response()->json([
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'event_date' => $event->event_date,
            'slug' => $event->slug,
            'owner' => $event->user->name ?? null
        ]);
    }
}
