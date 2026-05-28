<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Message;
use App\Models\PeerEvaluation;
use App\Models\Project;
use App\Models\Task;
use App\Models\SystemSetting;
use App\Notifications\UserCreatedNotification;
use App\Services\NextSmsService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $unreadMessages = Message::query()
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        $dashboardRole = $user->hasRole('admin') ? 'admin' : ($user->hasRole('supervisor') ? 'supervisor' : 'student');

        if ($dashboardRole === 'admin') {
            $userCount = User::count();
            $activeUserCount = User::active()->count();
            $onlineUserCount = User::query()->where('last_seen_at', '>', now()->subMinutes(5))->count();

            $groupCount = Group::count();
            $activeGroupCount = Group::active()->count();
            $projectCount = Project::count();
            $activeProjectCount = Project::active()->count();
            $taskCount = Task::count();

            $taskStatusCounts = Task::query()
                ->select('status', DB::raw('COUNT(*) as c'))
                ->groupBy('status')
                ->pluck('c', 'status');

            $recentUsers = User::query()->latest()->take(6)->get();
            $recentGroups = Group::query()->with(['supervisor', 'project'])->latest()->take(6)->get();

            $jobsPending = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();

            $authSettings = [
                'login_enabled' => SystemSetting::getBool('auth.login_enabled', true),
                'password_reset_enabled' => SystemSetting::getBool('auth.password_reset_enabled', true),
                'registration_enabled' => SystemSetting::getBool('auth.registration_enabled', true),
            ];

            return view('dashboard', compact(
                'dashboardRole',
                'unreadMessages',
                'userCount',
                'activeUserCount',
                'onlineUserCount',
                'groupCount',
                'activeGroupCount',
                'projectCount',
                'activeProjectCount',
                'taskCount',
                'taskStatusCounts',
                'recentUsers',
                'recentGroups',
                'jobsPending',
                'failedJobs',
                'authSettings',
            ));
        }

        if ($dashboardRole === 'supervisor') {
            $projectIds = Project::query()->where('supervisor_id', $user->id)->pluck('id');

            $myGroupCount = Group::query()->where('supervisor_id', $user->id)->count();
            $activeProjectCount = Project::query()->where('supervisor_id', $user->id)->active()->count();
            $overdueProjectCount = Project::query()->where('supervisor_id', $user->id)->overdue()->count();

            $overdueTaskCount = Task::query()
                ->whereIn('project_id', $projectIds)
                ->overdue()
                ->count();

            $pendingReviewTasks = Task::query()
                ->whereIn('project_id', $projectIds)
                ->where('status', 'review')
                ->count();

            $recentGroups = Group::query()
                ->where('supervisor_id', $user->id)
                ->with(['project'])
                ->withCount(['activeMembers'])
                ->latest()
                ->take(6)
                ->get();

            $recentEvaluations = PeerEvaluation::query()
                ->submitted()
                ->whereIn('project_id', $projectIds)
                ->with(['evaluator', 'evaluated', 'project'])
                ->latest('submitted_at')
                ->take(6)
                ->get();

            return view('dashboard', compact(
                'dashboardRole',
                'unreadMessages',
                'myGroupCount',
                'activeProjectCount',
                'overdueProjectCount',
                'overdueTaskCount',
                'pendingReviewTasks',
                'recentGroups',
                'recentEvaluations',
            ));
        }

        $activeMembership = $user->activeGroup()->with('group.activeMembers.user', 'group.project', 'group.supervisor')->first();
        $group = $activeMembership?->group;
        $project = $group?->project;

        $todoTaskCount = Task::query()
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['todo', 'in_progress', 'review'])
            ->count();

        $dueSoonCount = Task::query()
            ->where('assigned_to', $user->id)
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->whereNotIn('status', ['completed'])
            ->count();

        $upcomingTasks = Task::query()
            ->where('assigned_to', $user->id)
            ->whereNotIn('status', ['completed'])
            ->with('project')
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->latest('created_at')
            ->take(6)
            ->get();

        $surveyCompleted = (bool) ($user->studentSkillsSurvey?->completed_at);

        $evaluationTotalPeers = 0;
        $evaluationSubmitted = 0;
        $evaluationPending = 0;

        if ($group && $group->project_id) {
            $peerIds = $group->activeMembers
                ->pluck('user_id')
                ->filter()
                ->reject(fn ($id) => (int) $id === (int) $user->id)
                ->values();

            $evaluationTotalPeers = $peerIds->count();

            if ($evaluationTotalPeers > 0) {
                $evaluationSubmitted = PeerEvaluation::query()
                    ->where('evaluator_id', $user->id)
                    ->where('project_id', $group->project_id)
                    ->where('status', 'submitted')
                    ->count();
            }

            $evaluationPending = max(0, $evaluationTotalPeers - $evaluationSubmitted);
        }

        $groupMembers = $group
            ? $group->activeMembers()->with('user')->get()->pluck('user')->filter()->values()
            : collect();

        return view('dashboard', compact(
            'dashboardRole',
            'unreadMessages',
            'group',
            'project',
            'todoTaskCount',
            'dueSoonCount',
            'upcomingTasks',
            'surveyCompleted',
            'evaluationTotalPeers',
            'evaluationSubmitted',
            'evaluationPending',
            'groupMembers',
        ));
    }

    public function control()
    {
        abort_unless(auth()->user() && auth()->user()->hasRole('admin'), 403);

        SystemSetting::setDefault('auth.login_enabled', true);
        SystemSetting::setDefault('auth.password_reset_enabled', true);
        SystemSetting::setDefault('auth.registration_enabled', true);
        SystemSetting::setDefault('notify.email_enabled', true);
        SystemSetting::setDefault('notify.sms_enabled', true);

        $settings = [
            'login_enabled' => SystemSetting::getBool('auth.login_enabled', true),
            'password_reset_enabled' => SystemSetting::getBool('auth.password_reset_enabled', true),
            'registration_enabled' => SystemSetting::getBool('auth.registration_enabled', true),
            'email_enabled' => SystemSetting::getBool('notify.email_enabled', true),
            'sms_enabled' => SystemSetting::getBool('notify.sms_enabled', true),
        ];

        $jobsPending = DB::table('jobs')->count();
        $failedJobs = DB::table('failed_jobs')->count();

        return view('admin.control', compact('settings', 'jobsPending', 'failedJobs'));
    }

    public function updateControl(Request $request)
    {
        abort_unless(auth()->user() && auth()->user()->hasRole('admin'), 403);

        $validated = $request->validate([
            'login_enabled' => 'required|boolean',
            'password_reset_enabled' => 'required|boolean',
            'registration_enabled' => 'required|boolean',
            'email_enabled' => 'required|boolean',
            'sms_enabled' => 'required|boolean',
        ]);

        SystemSetting::set('auth.login_enabled', (bool) $validated['login_enabled']);
        SystemSetting::set('auth.password_reset_enabled', (bool) $validated['password_reset_enabled']);
        SystemSetting::set('auth.registration_enabled', (bool) $validated['registration_enabled']);
        SystemSetting::set('notify.email_enabled', (bool) $validated['email_enabled']);
        SystemSetting::set('notify.sms_enabled', (bool) $validated['sms_enabled']);

        return back()->with('status', 'System settings updated successfully.');
    }

    public function sendSystemEmail(Request $request)
    {
        abort_unless(auth()->user() && auth()->user()->hasRole('admin'), 403);
        abort_if(!SystemSetting::getBool('notify.email_enabled', true), 403, 'Email sending is disabled by the administrator.');

        $validated = $request->validate([
            'audience' => 'required|in:all,student,supervisor,admin',
            'subject' => 'required|string|max:160',
            'message' => 'required|string|max:4000',
        ]);

        $users = $this->resolveAudienceUsers($validated['audience']);
        $emails = $users->pluck('email')->filter()->unique()->values();

        $queued = 0;
        foreach ($emails->chunk(50) as $chunk) {
            $toList = $chunk->values()->all();
            $payload = [
                'subject' => $validated['subject'],
                'body' => $validated['message'],
            ];

            dispatch(function () use ($toList, $payload) {
                foreach ($toList as $email) {
                    Mail::send('emails.system-broadcast', $payload, function ($m) use ($email, $payload) {
                        $m->to($email)->subject($payload['subject']);
                    });
                }
            });

            $queued += count($toList);
        }

        return back()->with('status', "Email queued to {$queued} recipient(s).");
    }

    public function sendSystemSms(Request $request)
    {
        abort_unless(auth()->user() && auth()->user()->hasRole('admin'), 403);
        abort_if(!SystemSetting::getBool('notify.sms_enabled', true), 403, 'SMS sending is disabled by the administrator.');

        $validated = $request->validate([
            'audience' => 'required|in:all,student,supervisor,admin',
            'message' => 'required|string|max:480',
        ]);

        $users = $this->resolveAudienceUsers($validated['audience']);
        $phones = $users->pluck('phone')->filter()->unique()->values();

        $queued = 0;
        foreach ($phones->chunk(80) as $chunk) {
            $toList = $chunk->values()->all();
            $text = $validated['message'];

            dispatch(function () use ($toList, $text) {
                $service = new NextSmsService();
                foreach ($toList as $phone) {
                    $service->sendSms($phone, $text);
                }
            });

            $queued += count($toList);
        }

        return back()->with('status', "SMS queued to {$queued} recipient(s).");
    }

    public function testSms(Request $request)
    {
        abort_unless(auth()->user() && auth()->user()->hasRole('admin'), 403);

        if (!SystemSetting::getBool('notify.sms_enabled', true)) {
            return back()->withErrors(['sms_test' => 'SMS sending is disabled by the administrator.']);
        }

        $validated = $request->validate([
            'phone' => 'required|string|max:32',
            'message' => 'required|string|max:480',
        ]);

        $service = new NextSmsService();
        $ok = $service->sendSms($validated['phone'], $validated['message']);

        if (!$ok) {
            return back()->withErrors(['sms_test' => 'Failed to send SMS. Check NextSMS configuration and logs.']);
        }

        return back()->with('status', 'Test SMS sent successfully.');
    }

    public function clearSystemCache()
    {
        abort_unless(auth()->user() && auth()->user()->hasRole('admin'), 403);

        Artisan::call('optimize:clear');

        return back()->with('status', 'System cache cleared.');
    }

    public function processQueueNow()
    {
        abort_unless(auth()->user() && auth()->user()->hasRole('admin'), 403);

        $before = DB::table('jobs')->count();

        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            '--tries' => 3,
            '--timeout' => 120,
            '--max-jobs' => 200,
            '--max-time' => 60,
        ]);

        $after = DB::table('jobs')->count();
        $processed = max(0, $before - $after);

        return back()->with('status', "Queue processed. Jobs handled: {$processed}. Remaining: {$after}.");
    }

    public function startQueueWorker()
    {
        abort_unless(auth()->user() && auth()->user()->hasRole('admin'), 403);

        $php = PHP_BINARY;
        $artisan = base_path('artisan');

        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = 'start "" /B "' . $php . '" "' . $artisan . '" queue:work --sleep=2 --tries=3 --timeout=120';
            @pclose(@popen($cmd, 'r'));
        } else {
            $cmd = 'nohup "' . $php . '" "' . $artisan . '" queue:work --sleep=2 --tries=3 --timeout=120 > /dev/null 2>&1 &';
            @exec($cmd);
        }

        return back()->with('status', 'Queue worker start command issued.');
    }

    public function clearPendingJobs()
    {
        abort_unless(auth()->user() && auth()->user()->hasRole('admin'), 403);

        $count = DB::table('jobs')->count();
        DB::table('jobs')->truncate();

        return back()->with('status', "Cleared pending jobs: {$count}.");
    }

    public function clearFailedJobs()
    {
        abort_unless(auth()->user() && auth()->user()->hasRole('admin'), 403);

        $count = DB::table('failed_jobs')->count();
        DB::table('failed_jobs')->truncate();

        return back()->with('status', "Cleared failed jobs: {$count}.");
    }

    public function users()
    {
        $users = User::with(['roles', 'members.group'])->paginate(10);
        $totalUsers = User::count();
        $students = User::role('student')->count();
        $supervisors = User::role('supervisor')->count();
        $admins = User::role('admin')->count();
        
        return view('admin.index', compact('users', 'totalUsers', 'students', 'supervisors', 'admins'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:male,female,other',
            'role' => 'required|in:student,supervisor,admin',
            'registration_number' => 'nullable|string|max:50|unique:users,registration_number',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'name' => trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name']),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'registration_number' => $validated['registration_number'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'status' => 'active',
        ]);

        $user->assignRole($validated['role']);

        // Send notifications
        try {
            $notification = new UserCreatedNotification($validated['password']);
            $user->notify($notification);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Manual User Creation Notification failed: " . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'User created successfully']);
    }

    public function search(Request $request)
    {
        $query = User::with(['roles', 'members.group']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('registration_number', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->input('role') !== 'all') {
            $query->role($request->input('role'));
        }

        if ($request->has('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $users = $query->paginate(10);

        return response()->json([
            'html' => view('admin._users_table', compact('users'))->render(),
            'pagination' => (string) $users->links()
        ]);
    }

    public function showUser(User $user)
    {
        $user->load(['roles', 'members.group', 'studentProfile', 'supervisorProfile', 'studentSkillsSurvey']);
        
        // Add surveyed skills if they exist
        $surveyedSkills = [];
        if ($user->studentSkillsSurvey) {
            $skills = $user->studentSkillsSurvey->skills;
            $expLevel = ucfirst($user->studentSkillsSurvey->experience_level ?: 'Not specified');
            
            if (is_array($skills)) {
                foreach ($skills as $skill) {
                    $surveyedSkills[] = [
                        'name' => $skill,
                        'level' => $expLevel // Use the overall experience level
                    ];
                }
            }
        }
        $user->surveyed_skills = $surveyedSkills;
        
        return response()->json($user);
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'registration_number' => 'nullable|string|max:50|unique:users,registration_number,' . $user->id,
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $user->update([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'name' => trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name']),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'registration_number' => $validated['registration_number'],
            'status' => $validated['status'],
        ]);

        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }

    public function deleteUser(User $user)
    {
        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
            return response()->json(['success' => false, 'message' => 'You cannot delete your own account.'], 403);
        }

        try {
            // Delete related profiles and survey if they exist (though cascade should handle this if set)
            // But manually handling for safety or if no FK cascade is set
            $user->delete();
            return response()->json(['success' => true, 'message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete user: ' . $e->getMessage()], 500);
        }
    }

    public function resetPassword(User $user)
    {
        try {
            $newPassword = \Illuminate\Support\Str::random(10);
            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($newPassword)
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Password reset successfully',
                'new_password' => $newPassword
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to reset password: ' . $e->getMessage()], 500);
        }
    }

    /* ═══════════════════════════════════════════
       GROUP MANAGEMENT
    ═══════════════════════════════════════════ */

    public function groups()
    {
        $groups = Group::with(['members.user', 'project', 'supervisor'])->paginate(10);
        $projects = Project::all();
        $supervisors = User::role('supervisor')->get();
        $students = User::role('student')->get();
        
        return view('admin.groups', compact('groups', 'projects', 'supervisors', 'students'));
    }

    public function searchGroups(Request $request)
    {
        $query = Group::with(['members.user', 'project', 'supervisor']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $groups = $query->paginate(10);

        return response()->json([
            'html' => view('admin._groups_table', compact('groups'))->render(),
            'pagination' => (string) $groups->links()
        ]);
    }

    public function updateGroup(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'max_members' => 'required|integer|min:2|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $group->update($validated);

        return response()->json(['success' => true, 'message' => 'Group updated successfully']);
    }

    public function deleteGroup(Group $group)
    {
        try {
            $group->delete();
            return response()->json(['success' => true, 'message' => 'Group deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete group: ' . $e->getMessage()], 500);
        }
    }

    public function deleteAllGroups()
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Clear relationships first to avoid FK constraints
            GroupMember::query()->delete();
            \App\Models\Message::whereNotNull('group_id')->delete();
            
            // Now delete all groups
            Group::query()->delete();

            \Illuminate\Support\Facades\DB::commit();
            
            return response()->json(['success' => true, 'message' => 'All groups and their memberships have been cleared successfully.']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete all groups: ' . $e->getMessage()], 500);
        }
    }

    public function addGroupMember(Request $request, Group $group)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:leader,member',
        ]);

        // Check if user is already in any group
        if (GroupMember::where('user_id', $validated['user_id'])->where('status', 'joined')->exists()) {
            return response()->json(['success' => false, 'message' => 'User is already a member of a group.'], 422);
        }

        // Check if group is full
        if ($group->members->count() >= $group->max_members) {
            return response()->json(['success' => false, 'message' => 'Group is already full.'], 422);
        }

        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $validated['user_id'],
            'role' => $validated['role'],
            'status' => 'joined',
            'joined_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Member added successfully']);
    }

    public function removeGroupMember(GroupMember $member)
    {
        try {
            $member->delete();
            return response()->json(['success' => true, 'message' => 'Member removed successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to remove member.'], 500);
        }
    }

    public function assignSupervisor(Request $request, Group $group)
    {
        $validated = $request->validate([
            'supervisor_id' => 'required|exists:users,id',
        ]);

        $group->update([
            'supervisor_id' => $validated['supervisor_id']
        ]);

        return response()->json(['success' => true, 'message' => 'Supervisor assigned successfully']);
    }

    private function resolveAudienceUsers(string $audience)
    {
        $query = User::query()->where('status', 'active');

        if ($audience !== 'all') {
            $query->role($audience);
        }

        return $query->get();
    }
}
