<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'user_two_id' => 'required|exists:users,id'
        ]);

        $authId = auth()->id();
        $otherUserId = $request->user_two_id;

        if ($authId === $otherUserId) {
            return response()->json(['message' => 'Cannot start a conversation with yourself'], 422);
        }

        $existing = Conversation::where(function ($q) use ($authId, $otherUserId) {
                $q->where('user_one_id', $authId)->where('user_two_id', $otherUserId);
            })
            ->orWhere(function ($q) use ($authId, $otherUserId) {
                $q->where('user_one_id', $otherUserId)->where('user_two_id', $authId);
            })
            ->first();

        if ($existing) {
            return response()->json($existing);
        }
        $conversation = Conversation::create([
            'user_one_id' => $authId,
            'user_two_id' => $otherUserId,
        ]);

        return response()->json($conversation, 201);
    }

    
}