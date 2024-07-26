<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class ChatsController extends Controller
{
    public function getChats()
    {
        $user_id = Auth::id();

        $chats = Chat::where('user_id', $user_id)
                     ->orWhere('participant_id', $user_id)
                     ->get();

        return response()->json($chats);
    }
}
