<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Workspace' }} - College Complaint Portal</title>

    <!-- Inter Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-[#F8FAFC] text-slate-900" x-data="{ mobileMenuOpen: false }">
    <div class="min-h-full flex flex-col lg:flex-row">
        <!-- Desktop Sidebar -->
        <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:shrink-0 bg-[#0B0F19] text-white border-r border-slate-800">
            <!-- Brand Typographic Header (No Logo Boxes) -->
            <div class="h-16 flex items-center px-6 border-b border-slate-800 bg-[#070A12]">
                <a href="{{ route('home') }}" class="group block">
                    <span class="block text-xs font-bold tracking-wider text-white uppercase group-hover:text-blue-400 transition leading-tight">
                        College Complaint Portal
                    </span>
                    <span class="block text-[10px] text-slate-400 font-medium tracking-wide">
                        Grievance Redressal
                    </span>
                </a>
            </div>

            <!-- Role Badge Banner -->
            <div class="px-6 py-2.5 border-b border-slate-800 bg-[#0E1424]">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-300 capitalize">
                        {{ auth()->user()->role === 'management' ? 'Management' : ucfirst(auth()->user()->role) }} Portal
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-950 text-blue-300 border border-blue-800 uppercase">
                        {{ auth()->user()->role }}
                    </span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-xs font-medium">
                @if(auth()->user()->isStudent())
                    <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('student.dashboard') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('student.complaints.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('student.complaints.create') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Submit Complaint</span>
                    </a>
                    <a href="{{ route('student.complaints.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('student.complaints.index*') && !request()->routeIs('student.complaints.create') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span>My Complaints</span>
                    </a>
                    <a href="{{ route('student.notifications.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded transition {{ request()->routeIs('student.notifications*') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="bell" class="w-4 h-4"></i>
                            <span>Notifications</span>
                        </div>
                        @if(($unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count()) > 0)
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-blue-500 text-white">{{ $unreadCount }}</span>
                        @endif
                    </a>

                @elseif(auth()->user()->isManagement())
                    <a href="{{ route('management.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('management.dashboard') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('management.assigned.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('management.assigned*') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="inbox" class="w-4 h-4"></i>
                        <span>Assigned Complaints</span>
                    </a>
                    <a href="{{ route('management.verification-status') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('management.verification-status') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="check-square" class="w-4 h-4"></i>
                        <span>Verification Status</span>
                    </a>
                    <a href="{{ route('management.closed') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('management.closed') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="archive" class="w-4 h-4"></i>
                        <span>Closed Complaints</span>
                    </a>
                    <a href="{{ route('management.notifications.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded transition {{ request()->routeIs('management.notifications*') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="bell" class="w-4 h-4"></i>
                            <span>Notifications</span>
                        </div>
                        @if(($unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count()) > 0)
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-blue-500 text-white">{{ $unreadCount }}</span>
                        @endif
                    </a>

                @elseif(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.complaints.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.complaints.index*') && !request()->routeIs('admin.verification-queue') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="folder" class="w-4 h-4"></i>
                        <span>All Complaints</span>
                    </a>
                    <a href="{{ route('admin.verification-queue') }}" class="flex items-center justify-between px-3 py-2.5 rounded transition {{ request()->routeIs('admin.verification-queue') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>Verification Queue</span>
                        </div>
                        @if(($queueCount = \App\Models\Complaint::where('status', 'RESOLUTION_SUBMITTED')->count()) > 0)
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-blue-500 text-white">{{ $queueCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.management.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.management*') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span>Management Accounts</span>
                    </a>
                    <a href="{{ route('admin.students.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.students*') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                        <span>Students</span>
                    </a>
                    <a href="{{ route('admin.departments.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.departments*') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="building" class="w-4 h-4"></i>
                        <span>Departments</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.categories*') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="tag" class="w-4 h-4"></i>
                        <span>Categories</span>
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.reports*') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                        <span>Reports & Analytics</span>
                    </a>
                    <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.audit-logs*') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="shield-alert" class="w-4 h-4"></i>
                        <span>Audit Logs</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.settings*') ? 'bg-blue-600 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                        <span>System Settings</span>
                    </a>
                @endif

                <div class="pt-4 mt-4 border-t border-slate-800">
                    <a href="{{ route('profile') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-300 hover:bg-slate-800 hover:text-white transition {{ request()->routeIs('profile') ? 'bg-slate-800 text-white' : '' }}">
                        <i data-lucide="user" class="w-4 h-4"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="{{ route('password.change') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-300 hover:bg-slate-800 hover:text-white transition {{ request()->routeIs('password.change') ? 'bg-slate-800 text-white' : '' }}">
                        <i data-lucide="key" class="w-4 h-4"></i>
                        <span>Change Password</span>
                    </a>
                </div>
            </nav>

            <!-- User Info & Dedicated Logout UI (Sidebar Bottom) -->
            <div class="p-4 border-t border-slate-800 bg-[#070A12]">
                <div class="mb-3">
                    <div class="text-xs font-semibold text-white truncate">{{ auth()->user()->full_name }}</div>
                    <div class="text-[11px] text-slate-400 font-mono truncate">
                        {{ auth()->user()->register_number ?? auth()->user()->employee_id ?? auth()->user()->email }}
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded text-xs font-semibold text-slate-200 bg-slate-900 border border-slate-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Desktop Topbar with Dedicated Logout UI & User State -->
            <header class="hidden lg:flex h-14 bg-white border-b border-slate-200 px-8 items-center justify-between sticky top-0 z-20">
                <div class="flex items-center gap-2 text-xs">
                    <span class="font-bold text-slate-900 tracking-tight uppercase">College Complaint Portal</span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-slate-500 font-medium">{{ $title ?? 'Workspace' }}</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <span class="block text-xs font-semibold text-slate-900 leading-tight">{{ auth()->user()->full_name }}</span>
                        <span class="block text-[10px] text-slate-400 font-mono">{{ auth()->user()->register_number ?? auth()->user()->employee_id ?? auth()->user()->email }}</span>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                        {{ auth()->user()->role === 'management' ? 'Management' : auth()->user()->role }}
                    </span>
                    <!-- DEDICATED LOGOUT UI -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition shadow-2xs">
                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Mobile Topbar -->
            <header class="lg:hidden h-14 bg-[#0B0F19] text-white flex items-center justify-between px-4 border-b border-slate-800 sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="p-1.5 rounded text-slate-300 hover:bg-slate-800">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <span class="text-xs font-bold uppercase tracking-wider text-white">College Complaint Portal</span>
                </div>

                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800 border border-slate-700">
                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Mobile Navigation Drawer -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden" 
                 @click="mobileMenuOpen = false" 
                 style="display: none;"></div>

            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0B0F19] text-white flex flex-col lg:hidden"
                 style="display: none;">
                <div class="h-14 flex items-center justify-between px-6 border-b border-slate-800 bg-[#070A12]">
                    <span class="text-xs font-bold uppercase tracking-wider text-white">Portal Navigation</span>
                    <button @click="mobileMenuOpen = false" class="p-1 text-slate-400 hover:text-white">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="px-4 py-3 bg-[#0E1424] border-b border-slate-800 text-xs">
                    <div class="font-semibold text-white">{{ auth()->user()->full_name }}</div>
                    <div class="text-slate-400 text-[11px] font-mono">{{ auth()->user()->email }}</div>
                </div>
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto text-xs font-medium">
                    @if(auth()->user()->isStudent())
                        <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
                        </a>
                        <a href="{{ route('student.complaints.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i> Submit Complaint
                        </a>
                        <a href="{{ route('student.complaints.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="file-text" class="w-4 h-4"></i> My Complaints
                        </a>
                        <a href="{{ route('student.notifications.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="bell" class="w-4 h-4"></i> Notifications
                        </a>
                    @elseif(auth()->user()->isManagement())
                        <a href="{{ route('management.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
                        </a>
                        <a href="{{ route('management.assigned.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="inbox" class="w-4 h-4"></i> Assigned Complaints
                        </a>
                        <a href="{{ route('management.verification-status') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="check-square" class="w-4 h-4"></i> Verification Status
                        </a>
                        <a href="{{ route('management.closed') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="archive" class="w-4 h-4"></i> Closed Complaints
                        </a>
                        <a href="{{ route('management.notifications.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="bell" class="w-4 h-4"></i> Notifications
                        </a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
                        </a>
                        <a href="{{ route('admin.complaints.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="folder" class="w-4 h-4"></i> All Complaints
                        </a>
                        <a href="{{ route('admin.verification-queue') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="check-circle" class="w-4 h-4"></i> Verification Queue
                        </a>
                        <a href="{{ route('admin.management.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="users" class="w-4 h-4"></i> Management Accounts
                        </a>
                        <a href="{{ route('admin.students.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="graduation-cap" class="w-4 h-4"></i> Students
                        </a>
                        <a href="{{ route('admin.departments.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="building" class="w-4 h-4"></i> Departments
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="tag" class="w-4 h-4"></i> Categories
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="bar-chart-2" class="w-4 h-4"></i> Reports & Analytics
                        </a>
                        <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="shield-alert" class="w-4 h-4"></i> Audit Logs
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded text-slate-200 hover:bg-slate-800">
                            <i data-lucide="settings" class="w-4 h-4"></i> Settings
                        </a>
                    @endif
                    <div class="pt-3 border-t border-slate-800">
                        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-3 py-2 rounded text-slate-300 hover:bg-slate-800">
                            <i data-lucide="user" class="w-4 h-4"></i> Profile
                        </a>
                    </div>
                </nav>
                <!-- Mobile Drawer Logout Footer -->
                <div class="p-4 border-t border-slate-800 bg-[#070A12]">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded text-xs font-semibold text-slate-200 bg-slate-800 border border-slate-700 hover:bg-blue-600 hover:text-white transition">
                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main Page Content Header & Body -->
            <main class="flex-1 overflow-y-auto bg-[#F8FAFC]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <x-alert />
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>
</html>
