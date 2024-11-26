<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'from',
        'to',
        'subject',
        'body',
        'status',
        'received_at',
        'reply_id',
        'email_account_id'
    ];

    // Define the relationship with the Replies model
    public function reply()
    {
        return $this->belongsTo(Reply::class, 'reply_id');
    }

    public function emailAccount()
    {
        return $this->belongsTo(EmailAccount::class, 'email_account_id');
    }
}