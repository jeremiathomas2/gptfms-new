<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index()
    {
        return view('messages.index');
    }

    public function getChats()
    {
        $user = Auth::user();
        
        // Get group chats
        $groups = $user->groupMemberships()->with('group')->get()->pluck('group');
        
        // Get private chats (users who have sent/received messages)
        $privateChats = Message::where('sender_id', $user->id)
            ->whereNull('group_id')
            ->select('receiver_id as user_id')
            ->union(
                Message::where('receiver_id', $user->id)
                ->whereNull('group_id')
                ->select('sender_id as user_id')
            )
            ->get()
            ->pluck('user_id')
            ->unique();
            
        $users = User::whereIn('id', $privateChats)->get();

        return response()->json([
            'groups' => $groups,
            'users' => $users
        ]);
    }

    public function getMessages($type, $id)
    {
        $user = Auth::user();
        
        if ($type === 'group') {
            $messages = Message::where('group_id', $id)
                ->with('sender')
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $messages = Message::betweenUsers($user->id, $id)
                ->whereNull('group_id')
                ->with('sender')
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return response()->json($messages);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'type' => 'required|in:group,private',
            'target_id' => 'required|integer',
        ]);

        $messageData = [
            'sender_id' => Auth::id(),
            'content' => $validated['content'],
            'type' => 'text',
        ];

        if ($validated['type'] === 'group') {
            $messageData['group_id'] = $validated['target_id'];
        } else {
            $messageData['receiver_id'] = $validated['target_id'];
        }

        $message = Message::create($messageData);
        $message->load('sender');

        return response()->json($message);
    }
}
