<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center justify-content-center">
                <img src="{{ asset('assets/img/larashiz-logo.jpg') }}" alt="Larashiz" width="32" height="32" class="rounded-circle mr-2 shadow-sm">
                <span>LARASHIZ</span>
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center justify-content-center">
                <img src="{{ asset('assets/img/larashiz-logo.jpg') }}" alt="Larashiz" width="28" height="28" class="rounded-circle shadow-sm">
            </a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li class="nav-item dropdown {{ request()->routeIs('dashboard') || request()->is('shizuefi/index*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-fire"></i><span>Dashboard</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><a class="nav-link" href="{{ route('dashboard') }}">Main Dashboard</a></li>
                    <li class="{{ request()->is('shizuefi/index-0') ? 'active' : '' }}"><a class="nav-link" href="/shizuefi/index-0">General Dashboard</a></li>
                    <li class="{{ request()->is('shizuefi/index') ? 'active' : '' }}"><a class="nav-link" href="/shizuefi/index">Ecommerce Dashboard</a></li>
                </ul>
            </li>

            @auth
                @if (auth()->user()->hasRole('admin'))
                    <li class="menu-header">Administrator</li>
                    <li class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.users') }}">
                            <i class="fas fa-users-cog"></i> <span>Kelola Pengguna</span>
                        </a>
                    </li>
                @endif
            @endauth

            <li class="menu-header">Starter</li>
            <li class="nav-item dropdown {{ request()->is('shizuefi/layout*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-columns"></i> <span>Layout</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ request()->is('shizuefi/layout-default') ? 'active' : '' }}"><a class="nav-link" href="/shizuefi/layout-default">Default Layout</a></li>
                    <li class="{{ request()->is('shizuefi/layout-transparent') ? 'active' : '' }}"><a class="nav-link" href="/shizuefi/layout-transparent">Transparent Sidebar</a></li>
                    <li class="{{ request()->is('shizuefi/layout-top-navigation') ? 'active' : '' }}"><a class="nav-link" href="/shizuefi/layout-top-navigation">Top Navigation</a></li>
                </ul>
            </li>
            <li class="{{ request()->is('shizuefi/blank') ? 'active' : '' }}"><a class="nav-link" href="/shizuefi/blank"><i class="far fa-square"></i> <span>Blank Page</span></a></li>

            <li class="menu-header">UI Components</li>
            <li class="nav-item dropdown {{ request()->is('shizuefi/bootstrap*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-th"></i> <span>Bootstrap (20)</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="/shizuefi/bootstrap-alert">Alert</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-badge">Badge</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-breadcrumb">Breadcrumb</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-buttons">Buttons</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-card">Card</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-carousel">Carousel</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-collapse">Collapse</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-dropdown">Dropdown</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-form">Form</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-list-group">List Group</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-media-object">Media Object</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-modal">Modal</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-nav">Nav</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-navbar">Navbar</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-pagination">Pagination</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-popover">Popover</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-progress">Progress</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-table">Table</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-tooltip">Tooltip</a></li>
                    <li><a class="nav-link" href="/shizuefi/bootstrap-typography">Typography</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown {{ request()->is('shizuefi/components*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-th-large"></i> <span>Components (13)</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="/shizuefi/components-article">Article</a></li>
                    <li><a class="nav-link" href="/shizuefi/components-avatar">Avatar</a></li>
                    <li><a class="nav-link" href="/shizuefi/components-chat-box">Chat Box</a></li>
                    <li><a class="nav-link" href="/shizuefi/components-empty-state">Empty State</a></li>
                    <li><a class="nav-link" href="/shizuefi/components-gallery">Gallery</a></li>
                    <li><a class="nav-link" href="/shizuefi/components-hero">Hero</a></li>
                    <li><a class="nav-link" href="/shizuefi/components-multiple-upload">Multiple Upload</a></li>
                    <li><a class="nav-link" href="/shizuefi/components-pricing">Pricing</a></li>
                    <li><a class="nav-link" href="/shizuefi/components-statistic">Statistic</a></li>
                    <li><a class="nav-link" href="/shizuefi/components-tab">Tab</a></li>
                    <li><a class="nav-link" href="/shizuefi/components-table">Table</a></li>
                    <li><a class="nav-link" href="/shizuefi/components-user">User</a></li>
                    <li><a class="nav-link" href="/shizuefi/components-wizard">Wizard</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown {{ request()->is('shizuefi/forms*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="far fa-file-alt"></i> <span>Forms</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="/shizuefi/forms-advanced-form">Advanced Form</a></li>
                    <li><a class="nav-link" href="/shizuefi/forms-editor">Editor</a></li>
                    <li><a class="nav-link" href="/shizuefi/forms-validation">Validation</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown {{ request()->is('shizuefi/gmaps*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-map-marker-alt"></i> <span>Google Maps</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="/shizuefi/gmaps-advanced-route">Advanced Route</a></li>
                    <li><a class="nav-link" href="/shizuefi/gmaps-draggable-marker">Draggable Marker</a></li>
                    <li><a class="nav-link" href="/shizuefi/gmaps-geocoding">Geocoding</a></li>
                    <li><a class="nav-link" href="/shizuefi/gmaps-geolocation">Geolocation</a></li>
                    <li><a class="nav-link" href="/shizuefi/gmaps-marker">Marker</a></li>
                    <li><a class="nav-link" href="/shizuefi/gmaps-multiple-marker">Multiple Marker</a></li>
                    <li><a class="nav-link" href="/shizuefi/gmaps-route">Route</a></li>
                    <li><a class="nav-link" href="/shizuefi/gmaps-simple">Simple</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown {{ request()->is('shizuefi/modules*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-plug"></i> <span>Modules</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="/shizuefi/modules-calendar">Calendar</a></li>
                    <li><a class="nav-link" href="/shizuefi/modules-chartjs">ChartJS</a></li>
                    <li><a class="nav-link" href="/shizuefi/modules-datatables">DataTables</a></li>
                    <li><a class="nav-link" href="/shizuefi/modules-flag">Flag</a></li>
                    <li><a class="nav-link" href="/shizuefi/modules-font-awesome">Font Awesome</a></li>
                    <li><a class="nav-link" href="/shizuefi/modules-ion-icons">Ion Icons</a></li>
                    <li><a class="nav-link" href="/shizuefi/modules-owl-carousel">Owl Carousel</a></li>
                    <li><a class="nav-link" href="/shizuefi/modules-sparkline">Sparkline</a></li>
                    <li><a class="nav-link" href="/shizuefi/modules-sweet-alert">Sweet Alert</a></li>
                    <li><a class="nav-link" href="/shizuefi/modules-toastr">Toastr</a></li>
                    <li><a class="nav-link" href="/shizuefi/modules-vector-map">Vector Map</a></li>
                    <li><a class="nav-link" href="/shizuefi/modules-weather-icon">Weather Icon</a></li>
                </ul>
            </li>

            <li class="menu-header">Pages & Utilities</li>
            <li class="nav-item dropdown {{ request()->is('shizuefi/auth*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="far fa-user"></i> <span>Auth Demo</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="/shizuefi/auth-forgot-password">Forgot Password</a></li>
                    <li><a class="nav-link" href="/shizuefi/auth-login">Login</a></li>
                    <li><a class="nav-link" href="/shizuefi/auth-login-2">Login 2</a></li>
                    <li><a class="nav-link" href="/shizuefi/auth-register">Register</a></li>
                    <li><a class="nav-link" href="/shizuefi/auth-reset-password">Reset Password</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown {{ request()->is('shizuefi/errors*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-exclamation"></i> <span>Errors</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="/shizuefi/errors-503">503 Service Unavailable</a></li>
                    <li><a class="nav-link" href="/shizuefi/errors-403">403 Forbidden</a></li>
                    <li><a class="nav-link" href="/shizuefi/errors-404">404 Not Found</a></li>
                    <li><a class="nav-link" href="/shizuefi/errors-500">500 Internal Server</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown {{ request()->is('shizuefi/features*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-bicycle"></i> <span>Features</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="/shizuefi/features-activities">Activities</a></li>
                    <li><a class="nav-link" href="/shizuefi/features-post-create">Post Create</a></li>
                    <li><a class="nav-link" href="/shizuefi/features-posts">Posts</a></li>
                    <li><a class="nav-link" href="/shizuefi/features-profile">Profile</a></li>
                    <li><a class="nav-link" href="/shizuefi/features-settings">Settings</a></li>
                    <li><a class="nav-link" href="/shizuefi/features-setting-detail">Setting Detail</a></li>
                    <li><a class="nav-link" href="/shizuefi/features-tickets">Tickets</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown {{ request()->is('shizuefi/utilities*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-ellipsis-h"></i> <span>Utilities</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="/shizuefi/utilities-contact">Contact</a></li>
                    <li><a class="nav-link" href="/shizuefi/utilities-invoice">Invoice</a></li>
                    <li><a class="nav-link" href="/shizuefi/utilities-subscribe">Subscribe</a></li>
                </ul>
            </li>
            <li class="{{ request()->is('shizuefi/credits') ? 'active' : '' }}"><a class="nav-link" href="/shizuefi/credits"><i class="fas fa-pencil-ruler"></i> <span>Credits</span></a></li>
        </ul>
    </aside>
</div>
