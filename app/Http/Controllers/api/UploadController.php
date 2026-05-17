<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function upload(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'message' => 'Event not found'
            ], 404);
        }

        $request->validate([
            'guest_name' => 'required|string|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:51200'
        ]);

        // Create Guest      
        $guest = Guest::create([
            'event_id' => $event->id,
            'name' => $request->guest_name
        ]);

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

        return response()->json([
            'message' => 'Upload successful',
            'upload' => $upload
        ]);
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
        $upload = Upload::findOrFail($id);

        if (!$upload) {
            return response()->json([
                'message' => 'Upload not found'
            ], 404);
        }

        $upload->update([
            'status'=> $request->status
        ]);

        return response()->json([
            "message"=> "Status updated."
        ],200);
    }


    public function destroy(Request $request, $slug, $id)
    {
        $upload = Upload::findOrFail($id);

        if (!$upload) {
            return response()->json([
                'message' => 'Upload not found'
            ], 404);
        }

        $upload->delete();

        return response()->json([
            "message"=> "Upload Deleted."
        ],200);
    }
}
