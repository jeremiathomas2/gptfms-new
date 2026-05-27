<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\Task;
use App\Models\Skill;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding database...');

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        DB::table('activity_logs')->truncate();
        DB::table('peer_evaluations')->truncate();
        DB::table('notifications')->truncate();
        DB::table('messages')->truncate();
        DB::table('tasks')->truncate();
        DB::table('milestones')->truncate();
        DB::table('project_phases')->truncate();
        DB::table('projects')->truncate();
        DB::table('group_members')->truncate();
        DB::table('groups')->truncate();
        DB::table('student_profiles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('roles')->truncate();
        DB::table('users')->truncate();
        DB::table('skills')->truncate();

        // Seed roles first
        $this->seedRoles();

        // Seed skills
        $this->seedSkills();

        // Seed users
        $this->seedUsers();

        // Seed Tanzanian sample data
        $this->call(CreateTanzanianSampleData::class);

        // Seed groups
        $this->seedGroups();

        // Seed projects
        $this->seedProjects();

        // Seed tasks
        $this->seedTasks();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Database seeding completed.');
    }

    private function seedRoles(): void
    {
        $roles = [
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'supervisor', 'guard_name' => 'web'],
            ['name' => 'student', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::create($role);
        }
    }

    private function seedSkills(): void
    {
        $skills = [
            ['name' => 'JavaScript', 'category' => 'Technical'],
            ['name' => 'Python', 'category' => 'Technical'],
            ['name' => 'Java', 'category' => 'Technical'],
            ['name' => 'React', 'category' => 'Technical'],
            ['name' => 'Vue.js', 'category' => 'Technical'],
            ['name' => 'Node.js', 'category' => 'Technical'],
            ['name' => 'PHP', 'category' => 'Technical'],
            ['name' => 'Laravel', 'category' => 'Technical'],
            ['name' => 'MySQL', 'category' => 'Technical'],
            ['name' => 'PostgreSQL', 'category' => 'Technical'],
            ['name' => 'HTML/CSS', 'category' => 'Technical'],
            ['name' => 'Leadership', 'category' => 'Soft Skills'],
            ['name' => 'Communication', 'category' => 'Soft Skills'],
            ['name' => 'Teamwork', 'category' => 'Soft Skills'],
            ['name' => 'Problem Solving', 'category' => 'Soft Skills'],
            ['name' => 'Time Management', 'category' => 'Soft Skills'],
            ['name' => 'Critical Thinking', 'category' => 'Soft Skills'],
            ['name' => 'Project Management', 'category' => 'Soft Skills'],
            ['name' => 'Research', 'category' => 'Soft Skills'],
            ['name' => 'Writing', 'category' => 'Soft Skills'],
            ['name' => 'Presentation', 'category' => 'Soft Skills'],
            ['name' => 'UI/UX Design', 'category' => 'Design'],
            ['name' => 'Graphic Design', 'category' => 'Design'],
            ['name' => 'Database Design', 'category' => 'Technical'],
        ];

        foreach ($skills as $skill) {
            Skill::create($skill);
        }
    }

    private function seedUsers(): void
    {
        // Create admin user
        $admin = User::create([
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'email' => 'admin@gptfms.com',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $admin->assignRole('admin');

        // Create supervisor users
        $supervisors = [
            ['first_name' => 'John', 'last_name' => 'Smith', 'email' => 'john.smith@university.edu'],
            ['first_name' => 'Sarah', 'last_name' => 'Johnson', 'email' => 'sarah.johnson@university.edu'],
            ['first_name' => 'Michael', 'last_name' => 'Brown', 'email' => 'michael.brown@university.edu'],
        ];

        foreach ($supervisors as $supervisorData) {
            $supervisor = User::create([
                'first_name' => $supervisorData['first_name'],
                'last_name' => $supervisorData['last_name'],
                'email' => $supervisorData['email'],
                'password' => Hash::make('password'),
                'status' => 'active',
            ]);
            $supervisor->assignRole('supervisor');
        }

        // Create student users
        $students = [
            ['first_name' => 'Alice', 'last_name' => 'Wilson', 'email' => 'alice.wilson@student.edu', 'gpa' => 3.8],
            ['first_name' => 'Bob', 'last_name' => 'Taylor', 'email' => 'bob.taylor@student.edu', 'gpa' => 3.5],
            ['first_name' => 'Charlie', 'last_name' => 'Davis', 'email' => 'charlie.davis@student.edu', 'gpa' => 3.9],
            ['first_name' => 'Diana', 'last_name' => 'Miller', 'email' => 'diana.miller@student.edu', 'gpa' => 3.7],
            ['first_name' => 'Eve', 'last_name' => 'Anderson', 'email' => 'eve.anderson@student.edu', 'gpa' => 3.6],
            ['first_name' => 'Frank', 'last_name' => 'Thomas', 'email' => 'frank.thomas@student.edu', 'gpa' => 3.4],
            ['first_name' => 'Grace', 'last_name' => 'Jackson', 'email' => 'grace.jackson@student.edu', 'gpa' => 3.8],
            ['first_name' => 'Henry', 'last_name' => 'White', 'email' => 'henry.white@student.edu', 'gpa' => 3.5],
            ['first_name' => 'Iris', 'last_name' => 'Harris', 'email' => 'iris.harris@student.edu', 'gpa' => 3.9],
            ['first_name' => 'Jack', 'last_name' => 'Martin', 'email' => 'jack.martin@student.edu', 'gpa' => 3.3],
        ];

        foreach ($students as $index => $studentData) {
            $student = User::create([
                'first_name' => $studentData['first_name'],
                'last_name' => $studentData['last_name'],
                'email' => $studentData['email'],
                'password' => Hash::make('password'),
                'status' => 'active',
            ]);
            $student->assignRole('student');

            // Create student profile
            StudentProfile::create([
                'user_id' => $student->id,
                'student_id' => 'STU' . str_pad($student->id, 6, '0', STR_PAD_LEFT),
                'gpa' => $studentData['gpa'],
                'major' => ['Computer Science', 'Information Technology', 'Software Engineering'][array_rand([0, 1, 2])],
                'semester' => rand(1, 8),
                'bio' => 'Passionate student interested in collaborative projects and learning new technologies.',
                'preferred_group_size' => rand(3, 5),
                'skills' => [
                    ['name' => 'JavaScript', 'proficiency' => rand(3, 5)],
                    ['name' => 'Python', 'proficiency' => rand(2, 4)],
                    ['name' => 'Teamwork', 'proficiency' => rand(3, 5)],
                    ['name' => 'Communication', 'proficiency' => rand(3, 5)],
                ],
                'availability' => [
                    'monday' => ['time_slots' => [['start' => '09:00', 'end' => '17:00']]],
                    'tuesday' => ['time_slots' => [['start' => '09:00', 'end' => '17:00']]],
                    'wednesday' => ['time_slots' => [['start' => '09:00', 'end' => '17:00']]],
                    'thursday' => ['time_slots' => [['start' => '09:00', 'end' => '17:00']]],
                    'friday' => ['time_slots' => [['start' => '09:00', 'end' => '15:00']]],
                ],
                'personality_traits' => [
                    'work_style' => ['collaborative', 'independent', 'leader'][array_rand([0, 1, 2])],
                    'communication_preference' => ['written', 'verbal', 'visual'][array_rand([0, 1, 2])],
                    'time_management' => ['early_planner', 'last_minute', 'flexible'][array_rand([0, 1, 2])],
                    'conflict_resolution' => ['collaborative', 'compromising', 'avoidant'][array_rand([0, 1, 2])],
                ],
                'total_projects' => 0,
                'average_rating' => 0.00,
            ]);
        }
    }

    private function seedGroups(): void
    {
        $groups = [
            ['name' => 'Alpha Team', 'description' => 'Focused on web development projects'],
            ['name' => 'Beta Squad', 'description' => 'Specializing in mobile applications'],
            ['name' => 'Gamma Group', 'description' => 'Data science and machine learning projects'],
            ['name' => 'Delta Force', 'description' => 'Full-stack development team'],
        ];

        foreach ($groups as $index => $groupData) {
            $group = Group::create([
                'name' => $groupData['name'],
                'description' => $groupData['description'],
                'status' => 'active',
                'max_members' => 4,
                'created_by' => User::role('student')->skip($index * 2)->first()->id,
                'formation_criteria' => ['strategy' => 'skill_balance'],
                'formation_score' => rand(70, 95),
                'formed_at' => now(),
            ]);

            // Add members to groups
            $students = User::role('student')->skip($index * 2)->take(4)->get();
            foreach ($students as $memberIndex => $student) {
                GroupMember::create([
                    'group_id' => $group->id,
                    'user_id' => $student->id,
                    'role' => $memberIndex === 0 ? 'leader' : 'member',
                    'status' => 'joined',
                    'joined_at' => now(),
                ]);
            }
        }
    }

    private function seedProjects(): void
    {
        $projects = [
            [
                'title' => 'E-Learning Platform',
                'description' => 'Develop a comprehensive online learning platform with course management, video streaming, and interactive assessments.',
                'course_code' => 'CS401',
                'priority' => 'high',
                'start_date' => now()->addDays(-30),
                'end_date' => now()->addDays(60),
                'max_grade' => 100,
            ],
            [
                'title' => 'Task Management System',
                'description' => 'Create a collaborative task management application with real-time updates and team collaboration features.',
                'course_code' => 'CS402',
                'priority' => 'medium',
                'start_date' => now()->addDays(-15),
                'end_date' => now()->addDays(45),
                'max_grade' => 100,
            ],
            [
                'title' => 'Mobile Banking App',
                'description' => 'Develop a secure mobile banking application with biometric authentication and transaction management.',
                'course_code' => 'CS403',
                'priority' => 'high',
                'start_date' => now(),
                'end_date' => now()->addDays(90),
                'max_grade' => 100,
            ],
        ];

        foreach ($projects as $index => $projectData) {
            $group = Group::skip($index)->first();
            $supervisor = User::role('supervisor')->skip($index)->first();

            $project = Project::create([
                'title' => $projectData['title'],
                'description' => $projectData['description'],
                'supervisor_id' => $supervisor->id,
                'group_id' => $group->id,
                'status' => 'in_progress',
                'priority' => $projectData['priority'],
                'start_date' => $projectData['start_date'],
                'end_date' => $projectData['end_date'],
                'progress_percentage' => rand(20, 80),
                'requirements' => [
                    'Must use modern web technologies',
                    'Include comprehensive documentation',
                    'Implement security best practices',
                ],
                'deliverables' => [
                    'Source code repository',
                    'Deployment-ready application',
                    'User documentation',
                    'Technical documentation',
                ],
                'course_code' => $projectData['course_code'],
                'max_grade' => $projectData['max_grade'],
            ]);

            $group->update([
                'project_id' => $project->id,
                'supervisor_id' => $supervisor->id,
            ]);

            foreach (Project::PHASES as $n => $title) {
                $status = $n <= 2 ? 'approved' : ($n === 3 ? 'submitted' : 'not_started');
                ProjectPhase::create([
                    'project_id' => $project->id,
                    'phase_number' => $n,
                    'phase_title' => $title,
                    'submission' => $n === 1 ? $project->title : ($n === 2 ? 'Initial requirements drafted by the team.' : ($n === 3 ? 'Analysis draft submitted for review.' : null)),
                    'submitted_by' => $group->activeMembers()->first()?->user_id,
                    'submitted_at' => $status !== 'not_started' ? now()->subDays(2) : null,
                    'status' => $status,
                    'supervisor_notes' => $status === 'approved' ? 'Approved.' : null,
                    'reviewed_by' => $status === 'approved' ? $supervisor->id : null,
                    'reviewed_at' => $status === 'approved' ? now()->subDay() : null,
                ]);
            }

            $project->update(['progress_percentage' => round((2 / 6) * 100, 2)]);
        }
    }

    private function seedTasks(): void
    {
        $tasks = [
            [
                'title' => 'Setup Development Environment',
                'description' => 'Configure development tools and environment for project.',
                'priority' => 'high',
                'estimated_hours' => 8,
                'due_date' => now()->addDays(3),
            ],
            [
                'title' => 'Design Database Schema',
                'description' => 'Create comprehensive database design for application.',
                'priority' => 'high',
                'estimated_hours' => 12,
                'due_date' => now()->addDays(7),
            ],
            [
                'title' => 'Implement User Authentication',
                'description' => 'Develop secure user registration and login system.',
                'priority' => 'high',
                'estimated_hours' => 16,
                'due_date' => now()->addDays(14),
            ],
            [
                'title' => 'Create Frontend Components',
                'description' => 'Build reusable UI components for the application.',
                'priority' => 'medium',
                'estimated_hours' => 20,
                'due_date' => now()->addDays(21),
            ],
            [
                'title' => 'API Development',
                'description' => 'Develop RESTful API endpoints for backend functionality.',
                'priority' => 'medium',
                'estimated_hours' => 24,
                'due_date' => now()->addDays(28),
            ],
            [
                'title' => 'Testing and Quality Assurance',
                'description' => 'Perform comprehensive testing including unit tests and integration tests.',
                'priority' => 'medium',
                'estimated_hours' => 16,
                'due_date' => now()->addDays(35),
            ],
            [
                'title' => 'Documentation',
                'description' => 'Create comprehensive project documentation.',
                'priority' => 'low',
                'estimated_hours' => 12,
                'due_date' => now()->addDays(42),
            ],
            [
                'title' => 'Deployment Preparation',
                'description' => 'Prepare application for production deployment.',
                'priority' => 'high',
                'estimated_hours' => 8,
                'due_date' => now()->addDays(49),
            ],
        ];

        foreach ($tasks as $index => $taskData) {
            $project = Project::find($index % 3 + 1);
            $assignedTo = $project->group->activeMembers->skip($index % 4)->first()->user_id;

            Task::create([
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'project_id' => $project->id,
                'assigned_to' => $assignedTo,
                'created_by' => $project->supervisor_id,
                'status' => ['todo', 'in_progress', 'completed'][array_rand([0, 1, 2])],
                'priority' => $taskData['priority'],
                'estimated_hours' => $taskData['estimated_hours'],
                'actual_hours' => rand(0, $taskData['estimated_hours']),
                'due_date' => $taskData['due_date'],
                'order' => $index,
                'tags' => ['development', 'backend', 'frontend'][array_rand([0, 1, 2])],
            ]);
        }
    }
}
