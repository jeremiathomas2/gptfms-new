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
    private function previewFromMessage(?Message $m): string
    {
        if (!$m) {
            return '';
        }

        $text = trim((string) ($m->content ?? ''));
        if ($text === '' && !empty($m->attachments)) {
            $text = 'Attachment';
        }
        if ($text === '') {
            $text = '—';
        }

        $text = preg_replace('/\s+/', ' ', $text) ?? $text;
        $text = trim($text);

        if (mb_strlen($text) > 46) {
            $text = mb_substr($text, 0, 46) . '…';
        }

        return $text;
    }

    private function getPrivateChatPartnerIds(int $userId)
    {
        return Message::query()
            ->whereNull('group_id')
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->selectRaw('CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END AS partner_id', [$userId])
            ->pluck('partner_id')
            ->unique()
            ->values();
    }

    public function index()
    {
        return view('messages.index');
    }

    public function getChats()
    {
        $user = Auth::user();
        $groups = collect();
        $users = collect();
        $students = collect();
        $supervisors = collect();
        $isAdmin = false;
        
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

            $privatePartnerIds = $this->getPrivateChatPartnerIds($user->id)->reject(fn ($id) => (int) $id === (int) $user->id);
            if ($privatePartnerIds->isNotEmpty()) {
                $users = $users->concat(User::whereIn('id', $privatePartnerIds)->get());
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

            $privatePartnerIds = $this->getPrivateChatPartnerIds($user->id)->reject(fn ($id) => (int) $id === (int) $user->id);
            if ($privatePartnerIds->isNotEmpty()) {
                $users = $users->concat(User::whereIn('id', $privatePartnerIds)->get());
            }
        } elseif ($user->hasRole('admin')) {
            // Admin: Separate lists for Students, Supervisors, and Groups
            $groups = Group::with('members.user', 'supervisor')->get();
            $students = User::role('student')->get();
            $supervisors = User::role('supervisor')->get();
            $isAdmin = true;
        } else {
            // Others: Existing logic (all groups and existing private chats)
            $memberGroups = $user->groupMemberships()->with('group')->get()->pluck('group');
            $supervisedGroups = $user->supervisedGroups()->get();
            $groups = $memberGroups->concat($supervisedGroups)->unique('id')->filter();
            
            $privatePartnerIds = $this->getPrivateChatPartnerIds($user->id)->reject(fn ($id) => (int) $id === (int) $user->id);
            $users = $privatePartnerIds->isNotEmpty()
                ? User::whereIn('id', $privatePartnerIds)->get()
                : collect();
        }

        $userId = (int) $user->id;

        if ($isAdmin) {
            $groups = $groups->map(function ($g) use ($userId) {
                $last = Message::query()
                    ->where('group_id', $g->id)
                    ->latest('id')
                    ->first();

                $g->last_message_preview = $this->previewFromMessage($last);
                $g->last_message_at = $last?->created_at?->toIso8601String();
                $g->unread_count = Message::query()
                    ->where('group_id', $g->id)
                    ->where('sender_id', '!=', $userId)
                    ->where('is_read', false)
                    ->count();
                $g->last_message_sort = $last?->created_at?->timestamp ?? 0;
                return $g;
            })->sortByDesc('last_message_sort')->values();

            $students = $students->map(function ($u) use ($userId) {
                $last = Message::query()
                    ->betweenUsers($userId, (int) $u->id)
                    ->whereNull('group_id')
                    ->latest('id')
                    ->first();

                $u->last_message_preview = $this->previewFromMessage($last);
                $u->last_message_at = $last?->created_at?->toIso8601String();
                $u->unread_count = Message::query()
                    ->whereNull('group_id')
                    ->where('sender_id', (int) $u->id)
                    ->where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->count();
                $u->last_message_sort = $last?->created_at?->timestamp ?? 0;
                return $u;
            })->sortByDesc('last_message_sort')->values();

            $supervisors = $supervisors->map(function ($u) use ($userId) {
                $last = Message::query()
                    ->betweenUsers($userId, (int) $u->id)
                    ->whereNull('group_id')
                    ->latest('id')
                    ->first();

                $u->last_message_preview = $this->previewFromMessage($last);
                $u->last_message_at = $last?->created_at?->toIso8601String();
                $u->unread_count = Message::query()
                    ->whereNull('group_id')
                    ->where('sender_id', (int) $u->id)
                    ->where('receiver_id', $userId)
                    ->where('is_read', false)
                    ->count();
                $u->last_message_sort = $last?->created_at?->timestamp ?? 0;
                return $u;
            })->sortByDesc('last_message_sort')->values();

            return response()->json([
                'groups' => $groups,
                'students' => $students,
                'supervisors' => $supervisors,
            ]);
        }

        $groups = $groups->values()->map(function ($g) use ($userId) {
            $last = Message::query()
                ->where('group_id', $g->id)
                ->latest('id')
                ->first();

            $g->last_message_preview = $this->previewFromMessage($last);
            $g->last_message_at = $last?->created_at?->toIso8601String();
            $g->unread_count = Message::query()
                ->where('group_id', $g->id)
                ->where('sender_id', '!=', $userId)
                ->where('is_read', false)
                ->count();
            $g->last_message_sort = $last?->created_at?->timestamp ?? 0;
            return $g;
        })->sortByDesc('last_message_sort')->values();

        $users = $users->unique('id')->values()->map(function ($u) use ($userId) {
            $last = Message::query()
                ->betweenUsers($userId, (int) $u->id)
                ->whereNull('group_id')
                ->latest('id')
                ->first();

            $u->last_message_preview = $this->previewFromMessage($last);
            $u->last_message_at = $last?->created_at?->toIso8601String();
            $u->unread_count = Message::query()
                ->whereNull('group_id')
                ->where('sender_id', (int) $u->id)
                ->where('receiver_id', $userId)
                ->where('is_read', false)
                ->count();
            $u->last_message_sort = $last?->created_at?->timestamp ?? 0;
            return $u;
        })->sortByDesc('last_message_sort')->values();

        return response()->json([
            'groups' => $groups,
            'users' => $users,
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

        if ($type === 'group') {
            Message::query()
                ->where('group_id', $id)
                ->where('sender_id', '!=', (int) $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
        } else {
            Message::query()
                ->whereNull('group_id')
                ->where('sender_id', (int) $id)
                ->where('receiver_id', (int) $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
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
            if ((int) $validated['target_id'] === (int) $user->id) {
                return response()->json(['error' => 'Invalid recipient'], 422);
            }

            $receiver = User::find($validated['target_id']);
            if (!$receiver) {
                return response()->json(['error' => 'Recipient not found'], 404);
            }

            $messageData['receiver_id'] = $receiver->id;
        }

        $message = Message::create($messageData);
        $message->load('sender');

        return response()->json($message);
    }
}
