<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWaitList extends Model
{
    use HasFactory;

    protected $table = 'user_waitlist';

    protected $fillable = [
        'email',
        'text_question',
        'text_response',
    ];

    public static function emailAlreadySignedUp($email)
    {
        return self::where('email', $email)->exists();
    }

    public static function createSignup($email, $textQuestion, $textResponse)
    {
        return self::create([
            'email' => $email,
            'text_question' => $textQuestion,
            'text_response' => $textResponse,
        ]);
    }
}
