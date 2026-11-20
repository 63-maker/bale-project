<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CCMS') }} - College Complaint Management System</title>

    <!-- Inter Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-slate-50 text-slate-800 flex flex-col justify-between">
    <!-- Clean Minimal Navigation Header -->
    <header class="border-b border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded bg-[#172B4D] text-white flex items-center justify-center font-bold text-sm tracking-wide">
                    CCMS
                </div>
                <div>
                    <span class="block text-sm font-semibold tracking-tight text-[#172B4D]">Apex College CCMS</span>
                    <span class="block text-xs text-slate-500">Complaint Management System</span>
                </div>
            </a>

            <div class="flex items-center gap-4 text-sm font-medium">
                @auth
                    @if(auth()->user()->isStudent())
                        <a href="{{ route('student.dashboard') }}" class="text-[#2563EB] hover:underline">Dashboard</a>
                    @elseif(auth()->user()->isManagement())
                        <a href="{{ route('management.dashboard') }}" class="text-[#2563EB] hover:underline">Dashboard</a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-[#2563EB] hover:underline">Dashboard</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-900">Sign In</a>
                    <a href="{{ route('register') }}" class="px-3.5 py-1.5 rounded bg-[#2563EB] text-white text-xs font-semibold hover:bg-blue-700 transition">Student Register</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col justify-center py-10 px-4 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>

    <!-- Clean Enterprise Footer -->
    <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p>&copy; {{ date('Y') }} Apex Institute of Technology & Engineering. All rights reserved.</p>
            <p class="text-slate-400">Strictly for authorized college personnel and registered students.</p>
        </div>
    </footer>
</body>
</html>
