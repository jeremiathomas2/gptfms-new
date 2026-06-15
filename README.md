<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# GPTFMS — User Menu & Role Guide

This document explains the full system functionality, how to navigate the menus, and what each role (Admin / Supervisor / Student) is allowed to do.

## Roles

### Admin
- Full system access.
- Creates projects (one project per group) and assigns supervisors/groups.
- Controls system settings (login/reset enable/disable, SMS/email enable/disable).
- Manages queue tools (start worker, process now, clear pending/failed jobs).
- Broadcasts Email/SMS and can send a Test SMS to any number format.

### Supervisor
- Sees only supervised groups/projects.
- Reviews and approves/rejects student phase submissions.
- Assigns tasks to students in their supervised groups.

### Student
- Sees only their group/project information.
- Completes Skills Survey.
- Uses Messages and Tasks.
- **Project phases:** only the **Group Leader** can submit phases; other members are **view-only**.

## Navigation (Side Menu)

### Common (All authenticated users)
- **Dashboard** (`/`) — role-based overview.
- **Messages** (`/messages`) — group + private chat.
- **Tasks** (`/tasks`) — kanban board (To do / In progress / Completed).
- **Analytics** (`/reports`) — role-based charts and metrics.
- **Settings** (`/settings`) — profile + security.

### Student menu
- **My Group** (`/my-group`) — your current group and members.
- **Skills Survey** (`/survey`) — submit skills + interests (used in group formation).
- **Project** (`/projects`) — 6-phase workflow page (single project per group).

### Supervisor menu
- **Supervisor Hub** (`/supervisor`) — reviews pending phases, project status, latest submissions.
- **My Groups** (`/my-group`) — groups you supervise.
- **Projects** (`/projects`) — supervised projects list and links to phase review.

### Admin menu
- **Admin Control** (`/admin`) — system controls + messaging + queue tools.
- **Users** (`/users`) — user management and importing.
- **Groups** (`/groups`) — group list and preview.
- **Projects** (`/projects`) — create/assign projects (admin only).
- **Group Settings** (`/groups/settings`) — group formation and settings.

## Project (6 Stages / Phases)

Project phases (in order):
1. Project Title  
2. Gather Requirement  
3. Analysis  
4. Designing  
5. Development and Testing  
6. Deployment  

Rules:
- Students can **view all phases** even before approval.
- Only the **Group Leader** can **submit** a phase.
- Supervisor/Admin can **Approve** or **Request Changes**.
- A phase becomes “completed” only after **approval**.

## Tasks

Rules:
- Admin/Supervisor can create tasks and assign to students.
- Students cannot create tasks.
- Students can:
  - **Preview** task details.
  - **Accept** (moves from `todo` → `in_progress`).
  - **Mark as Readed** (moves to `completed`).

## Messages

Features:
- Group and private chats.
- Unread indicator on chat list:
  - Unread badge appears when new messages arrive.
  - Badge disappears after opening the chat (messages are marked as read).
- New/latest messages move the chat to the top of the list.

## Admin Control (Operations)

Admin Control page (`/admin`) includes:
- Toggles: enable/disable Login, Password Reset, Registration.
- Toggles: enable/disable Email sending and SMS sending.
- Queue tools: Start Queue Worker, Process Queue Now, Clear Pending, Clear Failed.
- Messaging tools: Broadcast Email, Broadcast SMS, Test SMS (enter any number format).

Note: In production hosting, queue workers must run continuously for queued SMS/Email sending.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
