<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function index(){
        $events=Event::all();

        return response()->json($events);
    }

    public function store(Request $request){
        Event::create([
            'user_id'=> 1,
            'title'=> $request->title,
            'slug' => \Str::slug($request->title),
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
}
