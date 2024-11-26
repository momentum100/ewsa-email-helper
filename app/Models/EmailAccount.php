<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_address',
        'imap_user',
        'imap_pass',
        'imap_host',
        'imap_port',
        'imap_encryption',
        'smtp_user',
        'smtp_pass',
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
    ];

    /**
     * Get the user that owns the email account.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Add an email account to a user.
     *
     * @param array $data
     * @return EmailAccount
     */
    public static function addEmailAccountToUser($userId, $data)
    {
        $data['user_id'] = $userId;
        return self::create($data);
    }
}
