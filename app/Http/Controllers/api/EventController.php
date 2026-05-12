<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function store(Request $request){
        Event::create([
            'name'=> $request->name,
            'title'=> $request->tile,
            'description'=> $request->description,
            'event_date'=> $request->event_date,
        ])

        return response()->json({"message":"Sucess"});
    }
}
