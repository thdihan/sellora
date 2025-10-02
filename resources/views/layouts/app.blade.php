<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trim($__env->yieldContent('title') ?? '') ? $__env->yieldContent('title').' | ' : '' }}{{ config('app.name', 'MyApp') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/brand/favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/brand/favicon.svg') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="{{ asset('assets/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- FullCalendar JS -->
    <script src="{{ asset('assets/fullcalendar/fullcalendar.min.js') }}"></script>
    
    <style>
        :root {
            --sidebar-width: 280px;
            --topbar-height: 112px;
            --g-50: #f2fbf6;
            --g-100: #e6f5ea;
            --g-300: #cfead6;
            --g-500: #8fc59a;
            --g-600: #6fb482;
            --g-700: #4e9e67;
            --bg-gradient: linear-gradient(180deg, var(--g-50) 0%, var(--g-100) 35%, var(--g-300) 100%);
            --brand-gradient: linear-gradient(90deg, #7bc4a0 0%, #4aa56f 100%);
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: var(--g-50);
            --border-color: var(--g-300);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-gradient);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            color: #333;
            display: grid;
            grid-template-columns: var(--sidebar-width) 1fr;
            grid-template-rows: auto 1fr auto;
        }
        
        .sidebar {
            grid-column: 1 / 2;
            grid-row: 1 / 4;
            background: var(--brand-gradient);
            color: white;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
            box-shadow: 2px 0 15px rgba(123, 196, 160, 0.3);
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar-header .logo {
            background: white;
            padding: 12px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-header .logo img {
            width: 48px;
            height: 48px;
        }
        
        .sidebar-logo {
            filter: brightness(0) invert(1);
            flex-shrink: 0;
        }
        
        .digital-clock {
            margin-top: 15px;
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px 8px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .clock-time {
            font-size: 16px;
            font-weight: 600;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            margin-bottom: 4px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }
        
        .clock-date {
            font-size: 11px;
            font-weight: 500;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-item {
            margin: 5px 15px;
        }
        
        .menu-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 600;
            position: relative;
            background: linear-gradient(145deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 50%, rgba(0,0,0,0.05) 100%);
            border: 1px solid rgba(255,255,255,0.15);
            box-shadow: 
                0 4px 8px rgba(0,0,0,0.1),
                inset 0 1px 0 rgba(255,255,255,0.2),
                inset 0 -1px 0 rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        
        .menu-link:hover, .menu-link.active {
            background: linear-gradient(145deg, rgba(255,255,255,0.25) 0%, rgba(255,255,255,0.15) 50%, rgba(0,0,0,0.05) 100%);
            color: white;
            transform: translateX(8px) translateY(-2px);
            border-left: 4px solid #b8dfc2;
            box-shadow: 
                0 8px 25px rgba(0,0,0,0.2),
                0 4px 10px rgba(184, 223, 194, 0.3),
                inset 0 2px 0 rgba(255,255,255,0.3),
                inset 0 -2px 0 rgba(0,0,0,0.15);
            border-color: rgba(255,255,255,0.3);
        }
        
        .menu-link:active {
            transform: translateX(6px) translateY(0px);
            box-shadow: 
                0 4px 15px rgba(0,0,0,0.15),
                0 2px 5px rgba(184, 223, 194, 0.2),
                inset 0 1px 0 rgba(255,255,255,0.2),
                inset 0 -1px 0 rgba(0,0,0,0.1);
        }
        
        .menu-divider {
            margin: 20px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .menu-section-title {
            padding: 0 20px;
            margin-bottom: 10px;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .menu-link i {
            width: 20px;
            margin-right: 15px;
            text-align: center;
        }
        
        .topbar {
            grid-column: 2 / 3;
            grid-row: 1 / 2;
            height: var(--topbar-height);
            background: var(--bg-gradient);
            border-bottom: 2px solid var(--g-500);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 2px 15px rgba(143, 197, 154, 0.2);
        }
        
        .topbar .logo {
            background: white;
            padding: 12px 20px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .topbar .logo img {
            width: 58px;
            height: 58px;
        }
        
        .topbar-search {
            flex: 1;
            max-width: 400px;
            margin: 0 20px;
        }
        
        .search-container {
            position: relative;
        }
        
        .search-input {
            padding-left: 40px;
            border-radius: 25px;
            border: 2px solid var(--g-500);
            background-color: white;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            border-color: var(--g-600);
            box-shadow: 0 0 0 0.2rem rgba(143, 197, 154, 0.25);
            background-color: white;
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .notification-btn {
            position: relative;
            color: #6c757d;
            font-size: 1.2rem;
            padding: 8px;
        }
        
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .notification-menu {
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 0;
        }
        
        .notification-content {
            display: flex;
            flex-direction: column;
        }
        
        .notification-title {
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .notification-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .main-content {
            grid-column: 2 / 3;
            grid-row: 2 / 3;
            padding: 30px 30px 100px 30px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            overflow-y: auto;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            margin-left: auto;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 10px;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 30px;
        }
        

        
        .btn-primary {
            background: var(--brand-gradient);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(90deg, #6fb482 0%, #4e9e67 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(123, 196, 160, 0.3);
            color: white;
            filter: brightness(1.1);
        }
        
        .card {
            background: white;
            border: 1px solid var(--g-300);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            body {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr auto;
            }
            
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: var(--sidebar-width);
                height: 100vh;
                transform: translateX(-100%);
                z-index: 1001;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .topbar {
                grid-column: 1 / 2;
            }
            
            .main-content {
                grid-column: 1 / 2;
                padding: 20px 20px 100px 20px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar Navigation Component -->
    <x-sidebar-navigation />
    
    <!-- Topbar -->
    <div class="topbar">
        <!-- Logo in white rounded box -->
        <div class="logo">
            <img src="{{ asset('assets/brand/sellora-logo.png') }}" alt="Sellora Logo">
        </div>
        
        <button class="btn btn-link d-md-none" id="sidebarToggle">
            ☰
        </button>
        
        <!-- Search Bar -->
        <div class="topbar-search">
            <div class="search-container">
                <input type="text" class="form-control search-input" placeholder="Search..." id="globalSearch">
                🔍
            </div>
        </div>
        
        <!-- Notifications and User Info -->
        <div class="topbar-actions">
            <!-- Notifications -->
            @auth
            <div class="dropdown notification-dropdown">
                <button id="notif-btn" class="btn btn-link notification-btn" type="button" data-bs-toggle="dropdown">
                    🔔
                    <span id="notif-badge" class="notification-badge">{{ auth()->user()->unreadNotifications()->count() }}</span>
                </button>
                <ul class="dropdown-menu notification-menu dropdown-menu-end">
                    <li class="dropdown-header">Notifications</li>
                    @forelse(auth()->user()->unreadNotifications()->limit(5)->get() as $notification)
                        <li><a href="{{ $notification->data['url'] ?? '#' }}" class="dropdown-item notif-item" data-id="{{ $notification->id }}">
                            <div class="notification-item">
                                🔔
                                <div class="notification-content">
                                    <span class="notification-title">{{ $notification->data['title'] ?? 'Notification' }}</span>
                                    <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </a></li>
                    @empty
                        <li><span class="dropdown-item-text text-muted text-center">No new notifications</span></li>
                    @endforelse
                    <li><hr class="dropdown-divider"></li>
                    <li><button id="mark-all" class="dropdown-item text-center btn btn-link">Mark all as read</button></li>
                </ul>
            </div>
            @endauth
            
            <!-- User Info -->
            @auth
            <div class="user-info">
                <div class="user-avatar">
                    @if(auth()->user()->photo)
                        <img src="{{ asset('storage/' . auth()->user()->photo) }}" 
                             alt="{{ auth()->user()->name }}" 
                             class="rounded-circle" 
                             style="width: 40px; height: 40px; object-fit: cover;">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
                
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <strong>{{ auth()->user()->name }}</strong>
                        <br>
                        <small class="text-muted">{{ auth()->user()->role->name ?? 'User' }}</small>
                    </button>
                    
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.show') }}">👤 Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('settings.index') }}">⚙️ Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    🚪 Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            @endauth
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <span style="color: #28a745;">✓</span> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <span style="color: #dc3545;">⚠</span> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @yield('content')
        {{ $slot ?? '' }}
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
        
        // Scroll Position Preservation Utility
        window.ScrollPreserver = {
            save: function() {
                sessionStorage.setItem('scrollPosition', window.pageYOffset || document.documentElement.scrollTop);
            },
            restore: function() {
                const scrollPos = sessionStorage.getItem('scrollPosition');
                if (scrollPos) {
                    window.scrollTo(0, parseInt(scrollPos));
                    sessionStorage.removeItem('scrollPosition');
                }
            },
            preserveAndReload: function() {
                this.save();
                location.reload();
            }
        };

        // Restore scroll position on page load
        window.addEventListener('load', function() {
            ScrollPreserver.restore();
        });

        // Save scroll position before page unload
        window.addEventListener('beforeunload', function() {
            ScrollPreserver.save();
        });
        
        // Handle browser back/forward navigation
        window.addEventListener('popstate', function() {
            // Small delay to ensure page is rendered
            setTimeout(() => {
                ScrollPreserver.restore();
            }, 100);
        });
        
        // Enable automatic scroll restoration for browser navigation
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
        
        // Digital Clock Functionality
        function updateClock() {
            const now = new Date();
            const timeOptions = {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            const dateOptions = {
                weekday: 'short',
                month: 'short',
                day: 'numeric'
            };
            
            const timeElement = document.getElementById('clockTime');
            const dateElement = document.getElementById('clockDate');
            
            if (timeElement && dateElement) {
                timeElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
                dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
            }
        }
        
        // Global Search Functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize and start the clock
            updateClock();
            setInterval(updateClock, 1000);
            
            const searchInput = document.getElementById('globalSearch');
            
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        const query = this.value.trim();
                        if (query) {
                            // Redirect to search results page or perform search
                            window.location.href = `/search?q=${encodeURIComponent(query)}`;
                        }
                    }
                });
                
                // Optional: Add search suggestions dropdown
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    if (query.length > 2) {
                        // Here you can implement live search suggestions
                        // For now, we'll just show a simple implementation
                        console.log('Searching for:', query);
                    }
                });
            }
        });
        
        // Notification badge functionality
        function updateBadge(count) {
            const badge = document.getElementById('notif-badge');
            if (badge) {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'inline-flex' : 'none';
            }
        }
        
        // Handle individual notification clicks
        document.querySelectorAll('.notif-item').forEach(el => {
            el.addEventListener('click', async (e) => {
                e.preventDefault();
                const id = el.dataset.id;
                if (id) {
                    try {
                        const res = await fetch(`/notifications/${id}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (!res.ok) {
                            throw new Error(`HTTP error! status: ${res.status}`);
                        }
                        
                        const data = await res.json();
                        updateBadge(data.count);
                        
                        // Navigate to the URL if provided
                        const url = el.getAttribute('href');
                        if (url && url !== '#') {
                            window.location.href = url;
                        }
                    } catch (error) {
                        console.error('Error marking notification as read:', error);
                    }
                }
            });
        });
        
        // Handle mark all as read
        document.getElementById('mark-all')?.addEventListener('click', async () => {
            try {
                const res = await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                
                const data = await res.json();
                updateBadge(data.count);
                
                // Hide all notification items
                document.querySelectorAll('.notif-item').forEach(item => {
                    item.style.display = 'none';
                });
                
                // Show "no notifications" message
                const menu = document.querySelector('.notification-menu');
                const noNotifMsg = menu.querySelector('.dropdown-item-text');
                if (!noNotifMsg) {
                    const li = document.createElement('li');
                    li.innerHTML = '<span class="dropdown-item-text text-muted text-center">No new notifications</span>';
                    menu.insertBefore(li, menu.querySelector('.dropdown-divider'));
                }
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
            }
        });
        
        // Load initial unread count
        fetch('/notifications/unread-count', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(r => {
                if (!r.ok) {
                    throw new Error(`HTTP error! status: ${r.status}`);
                }
                return r.json();
            })
            .then(d => updateBadge(d.count))
            .catch(error => {
                console.error('Error fetching unread count:', error);
                // Hide badge on error
                updateBadge(0);
            });
    </script>
    
    @stack('scripts')
</body>
</html>
