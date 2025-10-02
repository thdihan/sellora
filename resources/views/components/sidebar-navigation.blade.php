<!-- Sidebar Navigation Component -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="{{ asset('assets/brand/sellora-logo.png') }}" alt="Sellora Logo" width="48" height="48">
        </div>
        <!-- Digital Clock -->
        <div class="digital-clock">
            <div class="clock-time" id="clockTime">00:00:00</div>
            <div class="clock-date" id="clockDate">Loading...</div>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <!-- Dashboard - Available to all users -->
        <div class="menu-item menu-single">
            <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                📊
                Dashboard
            </a>
        </div>
        
        <!-- Orders - Available to all users -->
        <div class="menu-item menu-single">
            <a href="{{ route('orders.index') }}" class="menu-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                🛒
                Orders
            </a>
        </div>
        
        <!-- Customers - Available to all users -->
        <div class="menu-item menu-single">
            <a href="{{ route('customers.index') }}" class="menu-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                👥
                Customers
            </a>
        </div>
        
        <!-- Bills - Available to all users -->
        <div class="menu-item menu-single">
            <a href="{{ route('bills.index') }}" class="menu-link {{ request()->routeIs('bills.*') ? 'active' : '' }}">
                💰
                Bills
            </a>
        </div>
        
        <!-- Expenses - Available to all users -->
        <div class="menu-item menu-single">
            <a href="{{ route('expenses.index') }}" class="menu-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                🧾
                Expenses
            </a>
        </div>
        
        <!-- Reports - Admin, Author, Manager and Chairman roles -->
        @if(auth()->check() && auth()->user()->role && in_array(auth()->user()->role->name, ['Admin', 'Author', 'Manager', 'Chairman']))
        <div class="menu-item menu-single">
            <a href="{{ route('reports.index') }}" class="menu-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                📈
                Reports
            </a>
        </div>
        @endif
        
        <!-- Visits - Available to all users -->
        <div class="menu-item menu-single">
            <a href="{{ route('visits.index') }}" class="menu-link {{ request()->routeIs('visits.*') ? 'active' : '' }}">
                📅
                Visits
            </a>
        </div>
        
        <!-- Budget - Manager and above -->
        @if(auth()->check() && auth()->user()->role && in_array(auth()->user()->role->name, ['Admin', 'Manager', 'Chairman', 'Finance Manager']))
        <div class="menu-item menu-single">
            <a href="{{ route('budgets.index') }}" class="menu-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
                💳
                Budget
            </a>
        </div>
        @endif
        
        <!-- Self Assessment - Available to all users -->
        <div class="menu-item menu-single">
            <a href="{{ route('self-assessments.index') }}" class="menu-link {{ request()->routeIs('self-assessments.*') ? 'active' : '' }}">
                ✅
                Self Assessment
            </a>
        </div>
        
        <!-- Sales Targets - Management roles and above -->
        @if(auth()->check() && auth()->user()->role && in_array(auth()->user()->role->name, ['Admin', 'Author', 'Chairman', 'Director', 'ED', 'GM', 'DGM', 'AGM', 'NSM', 'ZSM', 'RSM', 'ASM']))
        <div class="menu-item menu-single">
            <a href="{{ route('sales-targets.index') }}" class="menu-link {{ request()->routeIs('sales-targets.*') ? 'active' : '' }}">
                🎯
                Sales Targets
            </a>
        </div>
        @endif
        
        <!-- Events - Direct Link -->
        <div class="menu-item menu-single">
            <a href="{{ route('events.index') }}" class="menu-link {{ request()->routeIs('events.*') ? 'active' : '' }}">
                📅
                Events
            </a>
        </div>
        
        <!-- Location Tracker - Collapsible Parent -->
        @if(auth()->check() && auth()->user()->role && in_array(auth()->user()->role->name, ['Admin', 'Author', 'Chairman', 'Director', 'ED', 'GM', 'DGM', 'AGM', 'NSM', 'ZSM', 'RSM', 'ASM']))
        <div class="menu-item menu-parent" data-menu="location">
            <a href="#" class="menu-link" onclick="toggleMenu('location'); return false;">
                📍
                Location Tracker
            </a>
            <div class="submenu" id="submenu-location">
                <div class="menu-item">
                    <a href="{{ route('locations.index') }}" class="menu-link {{ request()->routeIs('locations.index') ? 'active' : '' }}">
                        📍
                        All Locations
                    </a>
                </div>
                <div class="menu-item">
                    <a href="{{ route('location.team-map') }}" class="menu-link {{ request()->routeIs('location.team-map') ? 'active' : '' }}">
                        👥
                        Team Map
                    </a>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Products - Author and Admin only -->
        @if(auth()->check() && auth()->user()->role && in_array(auth()->user()->role->name, ['Author', 'Admin']))
        <div class="menu-item menu-single">
            <a href="{{ route('products.index') }}" class="menu-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                📦
                Products
            </a>
        </div>
        

        
        <!-- Product Categories - Author and Admin only -->
        <div class="menu-item menu-single">
            <a href="{{ route('product-categories.index') }}" class="menu-link {{ request()->routeIs('product-categories.*') ? 'active' : '' }}">
                🏷️
                Categories
            </a>
        </div>
        
        <!-- Product Brands - Author and Admin only -->
        <div class="menu-item menu-single">
            <a href="{{ route('product-brands.index') }}" class="menu-link {{ request()->routeIs('product-brands.*') ? 'active' : '' }}">
                🏪
                Brands
            </a>
        </div>
        

        @endif
        
        <!-- Inventory - Available to all users -->
        <div class="menu-item menu-single">
            <a href="{{ route('inventory.index') }}" class="menu-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                🏭
                Inventory
            </a>
        </div>
        

        
        <!-- Assessments - Available to all users -->
        <div class="menu-item menu-single">
            <a href="{{ route('assessments.index') }}" class="menu-link {{ request()->routeIs('assessments.*') ? 'active' : '' }}">
                📋
                Assessments
            </a>
        </div>
        
        <!-- Presentations - Available to all users -->
        <div class="menu-item menu-single">
            <a href="{{ route('presentations.index') }}" class="menu-link {{ request()->routeIs('presentations.*') ? 'active' : '' }}">
                📊
                Presentations
            </a>
        </div>
        
        <!-- Admin Panel - Admin and Author only -->
        @if(auth()->check() && auth()->user()->role && in_array(auth()->user()->role->name, ['Admin', 'Author']))
        <div class="menu-section-divider"></div>
        
        <!-- Administration - Collapsible Parent -->
        <div class="menu-item menu-parent" data-menu="administration">
            <a href="#" class="menu-link" onclick="toggleMenu('administration'); return false;">
                ⚙️
                Administration
            </a>
            <div class="submenu" id="submenu-administration">
                <div class="menu-item">
                    <a href="{{ route('users.index') }}" class="menu-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        👥
                        User Management
                    </a>
                </div>
                <div class="menu-item">
                    <a href="{{ route('users.index') }}" class="menu-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        🛡️
                        Role Management
                    </a>
                </div>
                <div class="menu-item">
                    <a href="{{ route('taxes.index') }}" class="menu-link {{ request()->routeIs('taxes.*') ? 'active' : '' }}">
                        🧾
                        Tax Management
                    </a>
                </div>
                <div class="menu-item">
                    <a href="{{ route('api-connectors.index') }}" class="menu-link {{ request()->routeIs('api-connectors.*') ? 'active' : '' }}">
                        🔌
                        API Connector
                    </a>
                </div>
                <div class="menu-item">
                    <a href="{{ route('settings.index') }}" class="menu-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        💾
                        System Settings
                    </a>
                </div>

                <div class="menu-item">
                    <a href="{{ route('reports.export') }}" class="menu-link {{ request()->routeIs('reports.export') ? 'active' : '' }}">
                        📤
                        Data Export
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function toggleMenu(menuId) {
    const submenu = document.getElementById('submenu-' + menuId);
    const parentElement = submenu ? submenu.previousElementSibling : null;
    
    if (!submenu || !parentElement) return;
    
    const isExpanded = submenu.style.display === 'block';
    
    // Toggle aria-expanded attribute
    parentElement.setAttribute('aria-expanded', !isExpanded);
    
    // Toggle submenu visibility
    submenu.style.display = isExpanded ? 'none' : 'block';
    
    // Toggle chevron rotation if present
    const chevron = parentElement.querySelector('.chevron');
    if (chevron) {
        chevron.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(90deg)';
    }
}

// Initialize menu state on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if any submenu item is active and expand its parent
    const activeSubmenuItems = document.querySelectorAll('.submenu .menu-link.active');
    activeSubmenuItems.forEach(function(activeItem) {
        const submenu = activeItem.closest('.submenu');
        if (submenu) {
            const parentToggle = submenu.previousElementSibling;
            if (parentToggle && parentToggle.classList.contains('menu-parent')) {
                parentToggle.setAttribute('aria-expanded', 'true');
                submenu.style.display = 'block';
                const chevron = parentToggle.querySelector('.chevron');
                if (chevron) {
                    chevron.style.transform = 'rotate(90deg)';
                }
            }
        }
    });

    // Initialize all submenus to be hidden by default
    const allSubmenus = document.querySelectorAll('.submenu');
    allSubmenus.forEach(function(submenu) {
        if (!submenu.querySelector('.menu-link.active')) {
            submenu.style.display = 'none';
        }
    });
});
</script>