<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    public static function boot()
    {
        parent::boot();

        static::creating(function ($message) {
            $existingChat = Chat::where(function ($query) use ($message) {
                $query->where('user_id', $message->sender_id)
                    ->where('participant_id', $message->receiver_id);
            })->orWhere(function ($query) use ($message) {
                $query->where('user_id', $message->receiver_id)
                    ->where('participant_id', $message->sender_id);
            })->first();

            if (!$existingChat) {
                $chat = new Chat();
                $chat->user_id= $message->sender_id;
                $chat->participant_id = $message->receiver_id;
                $chat->save();
            }
        });
    }
}

