<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class ChatsController extends Controller
{
    public function getUserChats()
    {
        $user = Auth::user();
        $chats = Chat::where('user_id', $user->id)
            ->orWhere('participant_id', $user->id)
            ->get();

        $chatMessages = [];

        foreach ($chats as $chat) {
            $messages = Message::where(function($query) use ($chat) {
                $query->where('sender_id', $chat->user_id)
                      ->where('receiver_id', $chat->participant_id);
            })->orWhere(function($query) use ($chat) {
                $query->where('sender_id', $chat->participant_id)
                      ->where('receiver_id', $chat->user_id);
            })->get();
            $participantId = $chat->user_id === $user->id ? $chat->participant_id : $chat->user_id;
            $participant = User::find($participantId);

            $chatMessages[] = [
                'chat' => $chat,
                'participant_name' => $participant ? $participant->name : 'Unknown',
                'messages' => $messages
            ];
        }

        return response()->json(['data' => $chatMessages]);
    }

}
