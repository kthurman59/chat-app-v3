<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    public function sendMessage(request $request)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $message = auth()->user()->messages()->create([
            'content' => $request->content
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    public function getMessages()
    {
        return Message::with('user')->latest()->limit(50)->get()->reverse();
    }
}
