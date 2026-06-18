<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Event extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'event_date',
        'location',
        'upload_deadline',
        'gallery_visible',
    ];

    protected static function booted()
    {
        static::created(function ($event) {
            $event->slug = Str::slug($event->title) . '-' . $event->id;
            $event->save();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }
}
