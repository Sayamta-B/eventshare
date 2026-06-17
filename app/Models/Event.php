<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


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
