<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'College Complaint Portal' }}</title>

    <!-- Inter Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-[#F8FAFC] text-slate-900 flex flex-col justify-between">
    <!-- Clean Minimal Navigation Header -->
    <header class="border-b border-slate-200 bg-white sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="group">
                <span class="block text-sm font-bold tracking-tight text-slate-900 uppercase group-hover:text-blue-600 transition">
                    College Complaint Portal
                </span>
                <span class="block text-[10px] text-slate-500 font-medium tracking-wide">
                    Institutional Grievance Redressal System
                </span>
            </a>

            <div class="flex items-center gap-3 text-xs font-semibold">
                @auth
                    @if(auth()->user()->isStudent())
                        <a href="{{ route('student.dashboard') }}" class="px-3.5 py-1.5 rounded bg-blue-600 text-white hover:bg-blue-700 transition">Go to Dashboard</a>
                    @elseif(auth()->user()->isManagement())
                        <a href="{{ route('management.dashboard') }}" class="px-3.5 py-1.5 rounded bg-blue-600 text-white hover:bg-blue-700 transition">Go to Dashboard</a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-1.5 rounded bg-blue-600 text-white hover:bg-blue-700 transition">Go to Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-700 hover:bg-slate-100 transition flex items-center gap-1.5">
                            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-1.5 text-slate-600 hover:text-black transition">Sign In</a>
                    <a href="{{ route('register') }}" class="px-3.5 py-1.5 rounded bg-slate-900 text-white hover:bg-black transition">New Student Registration</a>
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
            <p>&copy; {{ date('Y') }} College Complaint Portal. All rights reserved.</p>
            <p class="text-slate-400">Strictly for authorized college personnel and enrolled students.</p>
        </div>
    </footer>
</body>
</html>
