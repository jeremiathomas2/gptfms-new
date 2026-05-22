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
        $groups = collect();
        $users = collect();
        
        if ($user->hasRole('student')) {
            // Student: Only their own group
            $membership = $user->groupMemberships()->where('status', 'joined')->with('group.supervisor')->first();
            
            if ($membership && $membership->group) {
                $group = $membership->group;
                $groups->push($group);
                
                // Add group members (excluding self)
                $memberIds = $group->members()->where('status', 'joined')->where('user_id', '!=', $user->id)->pluck('user_id');
                $groupMembers = User::whereIn('id', $memberIds)->get();
                $users = $users->concat($groupMembers);
                
                // Add supervisor
                if ($group->supervisor) {
                    $users->push($group->supervisor);
                }
            }
        } elseif ($user->hasRole('supervisor')) {
            // Supervisor: All supervised groups
            $groups = $user->supervisedGroups()->with('members.user')->get();
            
            // All students in those groups
            foreach ($groups as $group) {
                foreach ($group->members as $member) {
                    if ($member->user && $member->status === 'joined') {
                        $student = $member->user;
                        $student->group_name = $group->name; // Attach group name for the badge
                        $users->push($student);
                    }
                }
            }
        } elseif ($user->hasRole('admin')) {
            // Admin: Separate lists for Students, Supervisors, and Groups
            $groups = Group::with('members.user', 'supervisor')->get();
            
            $students = User::role('student')->get();
            $supervisors = User::role('supervisor')->get();
            
            return response()->json([
                'groups' => $groups,
                'students' => $students,
                'supervisors' => $supervisors
            ]);
        } else {
            // Others: Existing logic (all groups and existing private chats)
            $memberGroups = $user->groupMemberships()->with('group')->get()->pluck('group');
            $supervisedGroups = $user->supervisedGroups()->get();
            $groups = $memberGroups->concat($supervisedGroups)->unique('id')->filter();
            
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
        }

        return response()->json([
            'groups' => $groups->values(),
            'users' => $users->unique('id')->values()
        ]);
    }

    public function getMessages(Request $request, $type, $id)
    {
        $user = Auth::user();
        $sinceId = $request->query('since_id');
        
        $query = Message::with('sender');
        
        if ($type === 'group') {
            $group = Group::findOrFail($id);
            
            // Authorization
            $isMember = $group->members()->where('user_id', $user->id)->exists();
            $isSupervisor = $group->supervisor_id == $user->id;
            $isAdmin = $user->hasRole('admin');
            
            if (!$isMember && !$isSupervisor && !$isAdmin) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $query->where('group_id', $id);
        } else {
            $query->betweenUsers($user->id, $id)->whereNull('group_id');
        }

        if ($sinceId) {
            $query->where('id', '>', $sinceId);
        }

        $messages = $query->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'content' => 'nullable|string',
            'type' => 'required|in:group,private',
            'target_id' => 'required|integer',
            'attachments' => 'nullable|array',
        ]);

        $user = Auth::user();

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('messages/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            }
        }

        $messageData = [
            'sender_id' => $user->id,
            'content' => $validated['content'] ?? '',
            'type' => !empty($attachments) ? 'file' : 'text',
            'attachments' => $attachments,
        ];

        if ($validated['type'] === 'group') {
            $group = Group::findOrFail($validated['target_id']);
            
            // Authorization
            $isMember = $group->members()->where('user_id', $user->id)->exists();
            $isSupervisor = $group->supervisor_id == $user->id;
            $isAdmin = $user->hasRole('admin');
            
            if (!$isMember && !$isSupervisor && !$isAdmin) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $messageData['group_id'] = $validated['target_id'];
        } else {
            $messageData['receiver_id'] = $validated['target_id'];
        }

        $message = Message::create($messageData);
        $message->load('sender');

        return response()->json($message);
    }
}
