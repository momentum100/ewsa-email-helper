<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $fillable = [
        'to',
        'from',
        'origin_subject',
        'origin_body',
        'reply_subject',
        'reply_body',
        'status',
        'parent_id'
    ];

    // Define the relationship with the Email model
    public function parentEmail()
    {
        return $this->belongsTo(Email::class, 'parent_id');
    }
}

