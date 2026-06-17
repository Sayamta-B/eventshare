<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'event_id',
        'name',
        'session_token',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }
}
