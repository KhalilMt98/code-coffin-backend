<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller
{

    public function getMessages()
    {
        $user_id = Auth::id();

        $messages = Message::where('sender_id', $user_id)
                            ->orWhere('receiver_id', $user_id)
                            ->get();

        return response()->json($messages);
    }


    public function createMessage(Request $request)
    {
        $validated_data = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:255',
        ]);

        $message = new Message();
        $message->sender_id = Auth::id();
        $message->receiver_id = $validated_data['receiver_id'];
        $message->message = $validated_data['message'];
        $message->save();

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message
        ], 201);
    }


    public function updateMessage(Request $request, $id)
    {
        $validated_data = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        if ($message->sender_id != Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $message->message = $validated_data['message'];
        $message->save();

        return response()->json([
            'message' => 'Message updated successfully',
            'data' => $message
        ], 200);
    }


    public function deleteMessage($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        if ($message->sender_id != Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $message->delete();

        return response()->json(['message' => 'Message deleted successfully'], 200);
    }
}
