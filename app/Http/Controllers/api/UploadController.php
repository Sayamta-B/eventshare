<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;


// this should create a guest and upload
class UploadController extends Controller
{
    public function upload(Request $request, $slug) //'create' later?
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found'
            ], 404);
        }

        $token = $request->cookie('guest_token');
        $guest = null;

        $request->validate([
            'guest_name' => 'required|string|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:51200'
        ]);

        if($token){
            $guest = Guest::where('event_id', $event->id)
                ->where('session_token', $token)
                ->first();
        }

        if(!$guest){
            $token = Str::random(32);
            // Create Guest      
            $guest = Guest::create([
                'event_id' => $event->id,
                'name' => $request->guest_name,
                'session_token' => $token,
                'last_upload_at' => now()
            ]);
        }
        else {
            $guest->update([
                'last_upload_at' => now()
            ]);
        }

        // Store File in storage/public
        $path = $request->file('file')
            ->store('uploads', 'public');

        // Detect File Type
        $mime = $request->file('file')->getMimeType();

        $type = str_contains($mime, 'video')
            ? 'video'
            : 'photo';

        // Save Upload
        $upload = Upload::create([
            'event_id' => $event->id,
            'guest_id' => $guest->id,
            'file_path' => $path,
            'file_type' => $type,
            'status' => 'pending'
        ]);

        Http::post('http://192.168.1.200:5678/webhook/event-upload', [
            'organizer_email' => $event->user->email,
            'event_id' => $event->id,
            'event_title' => $event->title,
            'guest_name' => $guest->name,
            'file_type' => $type,
            'uploaded_at' => now(),
        ]);

        return response()->json([
            'message' => 'Upload successful',
            'upload' => $upload
        ])->cookie('guest_token', $guest->session_token, 60 * 24 * 30); // 30 days
    }


    public function index($slug)
    {
        $event = Event::with('uploads.guest')
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'uploads' => $event->uploads
        ]);
    }

    public function update(Request $request, $slug, $id)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $upload = Upload::where('event_id', $event->id)
            ->where('id', $id)
            ->firstOrFail();

        $upload->update([
            'status'=> $request->status,
            'approved_at' => $request->status === 'approved' ? now() : null,
            'reviewed_by' => $request->user()->id
        ]);

        return response()->json([
            "message"=> "Status updated."
        ],200);
    }


    public function destroy(Request $request, $slug, $id)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $upload = Upload::where('event_id', $event->id)
            ->where('id', $id)
            ->firstOrFail();

        $upload->delete();

        return response()->json([
            "message"=> "Upload Deleted."
        ],200);
    }
}
