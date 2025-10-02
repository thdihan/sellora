<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trim($__env->yieldContent('title') ?? '') ? $__env->yieldContent('title').' | ' : '' }}TailAdmin</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.ico') }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        :root {
            --primary-blue: #3C50E0;
            --primary-blue-dark: #2A3BB7;
            --text-dark: #1C2434;
            --text-gray: #64748B;
            --border-gray: #E2E8F0;
            --bg-gray: #F1F5F9;
            --success-green: #10B981;
            --warning-orange: #F59E0B;
            --danger-red: #F87171;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #F8FAFC;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            color: var(--text-dark);
        }
        
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: #FFFFFF;
            border-right: 1px solid #E2E8F0;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid #E2E8F0;
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .logo-icon {
            width: 32px;
            height: 32px;
            background: var(--primary-blue);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
        }
        
        .logo-text {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-section {
            margin-bottom: 32px;
        }
        
        .menu-title {
            padding: 0 20px 12px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .menu-item {
            margin: 2px 12px;
        }
        
        .menu-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: var(--text-gray);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 14px;
        }
        
        .menu-link:hover {
            background-color: #F1F5F9;
            color: var(--text-dark);
        }
        
        .menu-link.active {
            background-color: var(--primary-blue);
            color: white;
        }
        
        .menu-link i {
            width: 20px;
            margin-right: 12px;
            text-align: center;
            font-size: 16px;
        }
        
        .menu-badge {
            margin-left: auto;
            background: var(--success-green);
            color: white;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        
        .main-wrapper {
            margin-left: 280px;
            min-height: 100vh;
        }
        
        .header {
            background: white;
            border-bottom: 1px solid #E2E8F0;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: between;
            gap: 20px;
        }
        
        .search-container {
            flex: 1;
            max-width: 400px;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            font-size: 14px;
            background: #F8FAFC;
            transition: all 0.2s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-blue);
            background: white;
        }
        
        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-gray);
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-left: auto;
        }
        
        .header-btn {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            border: 1px solid #E2E8F0;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-gray);
            transition: all 0.2s ease;
            position: relative;
        }
        
        .header-btn:hover {
            background: #F8FAFC;
            color: var(--text-dark);
        }
        
        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: var(--danger-red);
            border-radius: 50%;
            border: 2px solid white;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #E2E8F0;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .user-menu:hover {
            background: #F8FAFC;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            line-height: 1.2;
        }
        
        .user-role {
            font-size: 12px;
            color: var(--text-gray);
            line-height: 1.2;
        }
        
        /* User Dropdown Styles */
        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 280px;
            background: white;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
            margin-top: 8px;
        }
        
        .user-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-header {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #F1F5F9;
        }
        
        .user-avatar-large {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
        }
        
        .user-details {
            flex: 1;
        }
        
        .user-name-large {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            line-height: 1.2;
            margin-bottom: 2px;
        }
        
        .user-email {
            font-size: 13px;
            color: var(--text-gray);
            line-height: 1.2;
        }
        
        .dropdown-divider {
            height: 1px;
            background: #F1F5F9;
            margin: 8px 0;
        }
        
        .dropdown-items {
            padding: 8px 0;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        
        .dropdown-item:hover {
            background: #F8FAFC;
            color: var(--primary-blue);
        }
        
        .dropdown-item i {
            width: 16px;
            text-align: center;
            color: var(--text-gray);
        }
        
        .dropdown-item:hover i {
            color: var(--primary-blue);
        }
        
        .logout-item {
            color: var(--danger-red) !important;
        }
        
        .logout-item:hover {
            background: #FEF2F2 !important;
            color: var(--danger-red) !important;
        }
        
        .logout-item i {
            color: var(--danger-red) !important;
        }
        
        .dropdown-form {
            margin: 0;
        }
        
        .submenu {
            padding-left: 20px;
            margin-top: 4px;
        }
        
        .submenu .menu-item {
            margin: 1px 0;
        }
        
        .submenu .menu-link {
            padding: 8px 16px;
            font-size: 13px;
        }
        
        .menu-link .fa-chevron-down {
            transition: transform 0.2s ease;
        }
        
        .main-content {
            padding: 24px;
            min-height: calc(100vh - 80px);
        }
        
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <div class="logo-icon">T</div>
                <div class="logo-text">TailAdmin</div>
            </div>
        </div>
        
        <nav class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-title">MAIN MENU</div>
                
                <!-- Dashboard - Available to all users -->
                <div class="menu-item">
                    <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </div>
                
                <!-- Orders - Available to all users -->
                <div class="menu-item">
                    <a href="{{ route('orders.index') }}" class="menu-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart"></i>
                        Orders
                    </a>
                </div>
                
                <!-- Customers - Available to all users -->
                <div class="menu-item">
                    <a href="{{ route('customers.index') }}" class="menu-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        Customers
                    </a>
                </div>
                
                <!-- Bills - Available to all users -->
                <div class="menu-item">
                    <a href="{{ route('bills.index') }}" class="menu-link {{ request()->routeIs('bills.*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Bills
                    </a>
                </div>
                
                <!-- Expenses - Available to all users -->
                <div class="menu-item">
                    <a href="{{ route('expenses.index') }}" class="menu-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                        <i class="fas fa-receipt"></i>
                        Expenses
                    </a>
                </div>
                
                <!-- Visits - Available to all users -->
                <div class="menu-item">
                    <a href="{{ route('visits.index') }}" class="menu-link {{ request()->routeIs('visits.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>
                        Visits
                    </a>
                </div>
                
                <!-- Events - Available to all users -->
                <div class="menu-item">
                    <a href="{{ route('events.index') }}" class="menu-link {{ request()->routeIs('events.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        Events
                    </a>
                </div>
                
                <!-- Inventory - Available to all users -->
                <div class="menu-item">
                    <a href="{{ route('inventory.index') }}" class="menu-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                        <i class="fas fa-warehouse"></i>
                        Inventory
                    </a>
                </div>
                
                <!-- Assessments - Available to all users -->
                <div class="menu-item">
                    <a href="{{ route('assessments.index') }}" class="menu-link {{ request()->routeIs('assessments.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i>
                        Assessments
                    </a>
                </div>
                
                <!-- Self Assessment - Available to all users -->
                <div class="menu-item">
                    <a href="{{ route('self-assessments.index') }}" class="menu-link {{ request()->routeIs('self-assessments.*') ? 'active' : '' }}">
                        <i class="fas fa-user-check"></i>
                        Self Assessment
                    </a>
                </div>
                
                <!-- Presentations - Available to all users -->
                <div class="menu-item">
                    <a href="{{ route('presentations.index') }}" class="menu-link {{ request()->routeIs('presentations.*') ? 'active' : '' }}">
                        <i class="fas fa-presentation"></i>
                        Presentations
                    </a>
                </div>
            </div>
            
            <!-- Management Section -->
            @if(auth()->check() && auth()->user()->role && in_array(auth()->user()->role->name, ['Admin', 'Author', 'Manager', 'Chairman', 'Director', 'ED', 'GM', 'DGM', 'AGM', 'NSM', 'ZSM', 'RSM', 'ASM']))
            <div class="menu-section">
                <div class="menu-title">MANAGEMENT</div>
                
                <!-- Reports - Admin, Author, Manager and Chairman roles -->
                @if(in_array(auth()->user()->role->name, ['Admin', 'Author', 'Manager', 'Chairman']))
                <div class="menu-item">
                    <a href="{{ route('reports.index') }}" class="menu-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        Reports
                    </a>
                </div>
                @endif
                
                <!-- Budget - Manager and above -->
                @if(in_array(auth()->user()->role->name, ['Admin', 'Author', 'Manager', 'Chairman']))
                <div class="menu-item">
                    <a href="{{ route('budgets.index') }}" class="menu-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
                        <i class="fas fa-credit-card"></i>
                        Budget
                    </a>
                </div>
                @endif
                
                <!-- Sales Targets - Management roles and above -->
                <div class="menu-item">
                    <a href="{{ route('sales-targets.index') }}" class="menu-link {{ request()->routeIs('sales-targets.*') ? 'active' : '' }}">
                        <i class="fas fa-bullseye"></i>
                        Sales Targets
                    </a>
                </div>
                
                <!-- Location Tracker - Management roles -->
                <div class="menu-item menu-parent" data-menu="location">
                    <a href="#" class="menu-link" onclick="toggleMenu('location'); return false;">
                        <i class="fas fa-map-marker-alt"></i>
                        Location Tracker
                        <i class="fas fa-chevron-down ml-auto"></i>
                    </a>
                    <div class="submenu" id="submenu-location" style="display: none;">
                        <div class="menu-item">
                            <a href="{{ route('locations.index') }}" class="menu-link {{ request()->routeIs('locations.index') ? 'active' : '' }}">
                                <i class="fas fa-list"></i>
                                All Locations
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('location.team-map') }}" class="menu-link {{ request()->routeIs('location.team-map') ? 'active' : '' }}">
                                <i class="fas fa-users"></i>
                                Team Map
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Products Section - Author and Admin only -->
            @if(auth()->check() && auth()->user()->role && in_array(auth()->user()->role->name, ['Author', 'Admin']))
            <div class="menu-section">
                <div class="menu-title">PRODUCTS</div>
                
                <div class="menu-item">
                    <a href="{{ route('products.index') }}" class="menu-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i>
                        Products
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="{{ route('product-categories.index') }}" class="menu-link {{ request()->routeIs('product-categories.*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i>
                        Categories
                    </a>
                </div>
                
                <div class="menu-item">
                    <a href="{{ route('product-brands.index') }}" class="menu-link {{ request()->routeIs('product-brands.*') ? 'active' : '' }}">
                        <i class="fas fa-store"></i>
                        Brands
                    </a>
                </div>
            </div>
            @endif
            
            <!-- Administration Section - Admin and Author only -->
            @if(auth()->check() && auth()->user()->role && in_array(auth()->user()->role->name, ['Admin', 'Author']))
            <div class="menu-section">
                <div class="menu-title">ADMINISTRATION</div>
                
                <div class="menu-item menu-parent" data-menu="administration">
                    <a href="#" class="menu-link" onclick="toggleMenu('administration'); return false;">
                        <i class="fas fa-cogs"></i>
                        Administration
                        <i class="fas fa-chevron-down ml-auto"></i>
                    </a>
                    <div class="submenu" id="submenu-administration" style="display: none;">
                        <div class="menu-item">
                            <a href="{{ route('users.index') }}" class="menu-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="fas fa-users-cog"></i>
                                User Management
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('taxes.index') }}" class="menu-link {{ request()->routeIs('taxes.*') ? 'active' : '' }}">
                                <i class="fas fa-percentage"></i>
                                Tax Management
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('api-connectors.index') }}" class="menu-link {{ request()->routeIs('api-connectors.*') ? 'active' : '' }}">
                                <i class="fas fa-plug"></i>
                                API Connector
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('settings.index') }}" class="menu-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                <i class="fas fa-sliders-h"></i>
                                System Settings
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('reports.export') }}" class="menu-link {{ request()->routeIs('reports.export') ? 'active' : '' }}">
                                <i class="fas fa-download"></i>
                                Data Export
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-wrapper">
        <!-- Header -->
        <header class="header">
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Search or type command...">
                <i class="fas fa-search search-icon"></i>
            </div>
            
            <div class="header-actions">
                <button class="header-btn">
                    <i class="fas fa-moon"></i>
                </button>
                <button class="header-btn">
                    <i class="fas fa-bell"></i>
                    <div class="notification-badge"></div>
                </button>
                <div class="user-menu" id="user-menu">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name ?? 'User' }}</div>
                        <div class="user-role">{{ auth()->user()->role->name ?? 'User' }}</div>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                    
                    <!-- Dropdown Menu -->
                    <div class="user-dropdown" id="user-dropdown">
                        <div class="dropdown-header">
                            <div class="user-avatar-large">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                            <div class="user-details">
                                <div class="user-name-large">{{ auth()->user()->name ?? 'User' }}</div>
                                <div class="user-email">{{ auth()->user()->email ?? 'user@example.com' }}</div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-items">
                            <a href="{{ route('settings.profile') }}" class="dropdown-item">
                                <i class="fas fa-user"></i>
                                Profile
                            </a>
                            <a href="{{ route('settings.index') }}" class="dropdown-item">
                                <i class="fas fa-cog"></i>
                                Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" class="dropdown-form">
                                @csrf
                                <button type="submit" class="dropdown-item logout-item">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>
    
    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('open');
        }
        
        // Submenu toggle functionality
        function toggleMenu(menuId) {
            const submenu = document.getElementById('submenu-' + menuId);
            const menuParent = document.querySelector('[data-menu="' + menuId + '"]');
            const chevron = menuParent.querySelector('.fa-chevron-down');
            
            if (submenu.style.display === 'none' || submenu.style.display === '') {
                submenu.style.display = 'block';
                chevron.style.transform = 'rotate(180deg)';
            } else {
                submenu.style.display = 'none';
                chevron.style.transform = 'rotate(0deg)';
            }
        }
        
        // User dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const userMenu = document.getElementById('user-menu');
            const userDropdown = document.getElementById('user-dropdown');
            
            if (userMenu && userDropdown) {
                // Toggle dropdown on click
                userMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdown.classList.toggle('show');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userMenu.contains(e.target)) {
                        userDropdown.classList.remove('show');
                    }
                });
                
                // Prevent dropdown from closing when clicking inside it
                userDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });
        
        // Add mobile menu button if needed
        if (window.innerWidth <= 1024) {
            const header = document.querySelector('.header');
            const menuBtn = document.createElement('button');
            menuBtn.className = 'header-btn lg:hidden';
            menuBtn.innerHTML = '<i class="fas fa-bars"></i>';
            menuBtn.onclick = toggleSidebar;
            header.insertBefore(menuBtn, header.firstChild);
        }
    </script>
    
    @stack('scripts')
</body>
</html>
