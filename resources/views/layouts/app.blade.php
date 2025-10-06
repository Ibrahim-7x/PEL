<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PEL Project')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

    <!-- Page-specific styles -->
    @stack('styles')

    <!-- Page-specific meta tags -->
    @yield('meta')
</head>

<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div id="sidebar" class="vh-100 p-3 position-fixed top-0 start-0 d-flex flex-column overflow-hidden"
          style="width: 250px; transition: width 0.3s; background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%); box-shadow: 2px 0 10px rgba(0,0,0,0.1);">

        <!-- Logo + Text -->
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo abacus-1.png') }}"
                 alt="PEL Logo"
                 id="sidebarLogo"
                 class="img-fluid mb-2"
                 style="width:100px; height:100px; object-fit:contain; transition: all 0.3s ease; filter: drop-shadow(0 0 10px rgba(123, 143, 161, 0.3));">

            <h5 class="fw-bold text-white mb-1 sidebar-text" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);">PEL-Abacus</h5>
            <hr class="mt-3 mb-0 sidebar-text" style="border-top: 2px solid rgba(123, 143, 161, 0.5); width: 80%; margin: 0 auto;">
        </div>

        <!-- Sidebar Menu -->
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->is('welcome') ? 'active fw-bold' : '' }}" href="{{ url('/welcome') }}"
                   style="color: rgba(255,255,255,0.8); transition: all 0.3s ease; border-radius: 8px; padding: 10px 15px;">
                    <i class="bi bi-house-door me-2"></i><span class="sidebar-text">Home</span>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->is('home-agent') || request()->is('home-management') ? 'active fw-bold' : '' }}"
                   href="{{ auth()->check() ? (auth()->user()->role === 'Agent' ? url('/home-agent') : url('/home-management')) : route('login') }}"
                   style="color: rgba(255,255,255,0.8); transition: all 0.3s ease; border-radius: 8px; padding: 10px 15px;">
                    <i class="bi bi-clipboard-check me-2"></i><span class="sidebar-text">RU Case Registration</span>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->is('t-agent') || request()->is('t-management') ? 'active fw-bold' : '' }}"
                   href="{{ auth()->check() ? (auth()->user()->role === 'Agent' ? url('/t-agent') : url('/t-management')) : route('login') }}"
                   style="color: rgba(255,255,255,0.8); transition: all 0.3s ease; border-radius: 8px; padding: 10px 15px;">
                    <i class="bi bi-chat-dots me-2"></i><span class="sidebar-text">RU Case Tracking</span>
                </a>
            </li>
            @if(auth()->check() && strtolower(auth()->user()->role) === 'management')
                <li class="nav-item mt-3 mb-1 sidebar-text">
                    <span class="text-uppercase text-white small fw-bold" style="font-size: 11px; letter-spacing: 1px;">Reports</span>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{ route('export.initial_customer') }}" class="nav-link {{ request()->routeIs('export.initial_customer') ? 'active fw-bold' : '' }}"
                       style="color: rgba(255,255,255,0.8); transition: all 0.3s ease; border-radius: 8px; padding: 10px 15px;">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                        <span class="sidebar-text">Initial Customers</span>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="{{ route('export.happy_call') }}" class="nav-link {{ request()->routeIs('export.happy_call') ? 'active fw-bold' : '' }}"
                       style="color: rgba(255,255,255,0.8); transition: all 0.3s ease; border-radius: 8px; padding: 10px 15px;">
                        <i class="bi bi-emoji-smile me-2"></i>
                        <span class="sidebar-text">Happy Calls</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('export.feedback') }}" class="nav-link {{ request()->routeIs('export.feedback') ? 'active fw-bold' : '' }}"
                       style="color: rgba(255,255,255,0.8); transition: all 0.3s ease; border-radius: 8px; padding: 10px 15px;">
                        <i class="bi bi-chat-dots me-2"></i>
                        <span class="sidebar-text">Feedback</span>
                    </a>
                </li>
            @endif

        </ul>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 ms-250" id="mainContent" style="transition: margin-left 0.3s;">
        <!-- Top Navbar -->
        <nav class="navbar navbar-light bg-light border-bottom px-3 d-flex justify-content-between align-items-center sticky-top">
            <!-- Sidebar Toggle Button -->
            <button class="btn btn-outline-primary" id="toggleSidebar">☰</button>

            <!-- Profile Dropdown -->
            <div class="dropdown">
                @auth
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}" alt="Avatar" class="rounded-circle me-2" width="32" height="32">
                        <strong>{{ auth()->user()->name }}</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="{{ route('profile') }}">Go to Profile</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                @else
                    <a class="btn btn-primary" href="{{ route('login') }}">Login</a>
                @endauth
            </div>
        </nav>

        <!-- Session Warning Alert -->
        @if(session('session_warning'))
            <div class="alert alert-warning alert-dismissible fade show position-fixed"
                 style="top: 80px; right: 20px; z-index: 1050; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);"
                 role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Session Warning:</strong> {{ session('session_warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Page Content -->
        <div class="p-4">
            @yield('content')
        </div>
    </div>
</div>

<!-- Sidebar Toggle JS -->
<script>
    document.getElementById("toggleSidebar").addEventListener("click", function () {
        const sidebar = document.getElementById("sidebar");
        const mainContent = document.getElementById("mainContent");
        const logo = document.getElementById("sidebarLogo");

        if (sidebar.style.width === "60px") {
            // Expand sidebar
            sidebar.style.width = "250px";
            mainContent.style.marginLeft = "250px";
            logo.style.width = "100px";
            logo.style.height = "100px";

            // Show text
            setTimeout(() => {
                sidebar.querySelectorAll(".sidebar-text").forEach(el => el.classList.remove("d-none"));
            }, 200);
        } else {
            // Collapse sidebar
            sidebar.style.width = "60px";
            mainContent.style.marginLeft = "60px";
            logo.style.width = "40px";
            logo.style.height = "40px";

            // Hide text immediately
            sidebar.querySelectorAll(".sidebar-text").forEach(el => el.classList.add("d-none"));
        }
    });
</script>

<style>
    /* Expanded sidebar margin */
    .ms-250 {
        margin-left: 250px;
    }

    /* Sidebar Navigation Styles */
    #sidebar .nav-link {
        position: relative;
        overflow: hidden;
    }

    #sidebar .nav-link:hover {
        color: #7b8fa1 !important;
        background: rgba(123, 143, 161, 0.1);
        transform: translateX(5px);
    }

    #sidebar .nav-link.active {
        color: #7b8fa1 !important;
        background: rgba(123, 143, 161, 0.2);
        box-shadow: 0 2px 8px rgba(123, 143, 161, 0.3);
    }

    #sidebar .nav-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: #7b8fa1;
        transform: scaleY(0);
        transition: transform 0.3s ease;
        border-radius: 0 4px 4px 0;
    }

    #sidebar .nav-link:hover::before,
    #sidebar .nav-link.active::before {
        transform: scaleY(1);
    }

    /* Navbar styling */
    .navbar {
        background: linear-gradient(90deg, #2c3e50 0%, #34495e 100%) !important;
        border-bottom: 1px solid rgba(123, 143, 161, 0.2);
    }

    .navbar .btn-outline-primary {
        color: #7b8fa1;
        border-color: #7b8fa1;
        transition: all 0.3s ease;
    }

    .navbar .btn-outline-primary:hover {
        background: #7b8fa1;
        color: white;
        transform: scale(1.05);
    }

    /* Profile dropdown styling */
    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        border-radius: 10px;
    }

    .dropdown-item:hover {
        background: rgba(123, 143, 161, 0.1);
        color: #2c3e50;
    }
</style>

<!-- Page-specific JavaScript -->
@yield('scripts')

<!-- Session Timeout Handler -->
<script>
// Handle AJAX session timeouts
document.addEventListener('DOMContentLoaded', function() {
    // Intercept all AJAX requests
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch.apply(this, args).then(response => {
            if (response.status === 401) {
                return response.json().then(data => {
                    if (data.expired) {
                        // Show session expired message
                        showSessionExpiredAlert(data.message, data.redirect);
                        return Promise.reject(new Error('Session expired'));
                    }
                    return response;
                });
            }
            return response;
        });
    };

    // Handle XMLHttpRequest (for older AJAX calls)
    const originalOpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function(method, url, ...rest) {
        this.addEventListener('load', function() {
            if (this.status === 401) {
                try {
                    const data = JSON.parse(this.responseText);
                    if (data.expired) {
                        showSessionExpiredAlert(data.message, data.redirect);
                    }
                } catch (e) {
                    // If response is not JSON, redirect to login
                    window.location.href = '{{ route("login") }}';
                }
            }
        });
        return originalOpen.call(this, method, url, ...rest);
    };
});

function showSessionExpiredAlert(message, redirectUrl) {
    // Remove any existing alert
    const existingAlert = document.getElementById('session-expired-alert');
    if (existingAlert) {
        existingAlert.remove();
    }

    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.id = 'session-expired-alert';
    alertDiv.className = 'alert alert-danger position-fixed';
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 350px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);';
    alertDiv.innerHTML = `
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Session Expired!</strong> ${message}
        <button type="button" class="btn-close" onclick="redirectToLogin('${redirectUrl}')"></button>
    `;

    document.body.appendChild(alertDiv);

    // Auto redirect after 5 seconds
    setTimeout(() => {
        redirectToLogin(redirectUrl);
    }, 5000);
}

function redirectToLogin(url) {
    window.location.href = url || '{{ route("login") }}';
}

// Activity tracker to reset session timeout (optimized)
let lastActivity = Date.now();
let lastHeartbeat = 0;
const HEARTBEAT_THROTTLE = 2 * 60 * 1000; // 2 minutes minimum between heartbeats
let activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];

activityEvents.forEach(event => {
    document.addEventListener(event, function() {
        lastActivity = Date.now();
        // Store activity timestamp in sessionStorage
        sessionStorage.setItem('lastActivity', lastActivity.toString());
    }, { passive: true });
});

// Check activity periodically (every 5 minutes) and send heartbeat if active
setInterval(function() {
    const now = Date.now();
    const timeSinceLastActivity = now - lastActivity;
    const timeSinceLastHeartbeat = now - lastHeartbeat;

    // If user was active in the last 10 minutes and we haven't sent a heartbeat recently
    if (timeSinceLastActivity < 10 * 60 * 1000 && timeSinceLastHeartbeat > HEARTBEAT_THROTTLE) {
        // Send heartbeat to a dedicated endpoint (not profile)
        if (navigator.sendBeacon) {
            navigator.sendBeacon('{{ url("/heartbeat") }}', new FormData());
            lastHeartbeat = now;
        }
    }
}, 5 * 60 * 1000); // Check every 5 minutes
</script>

</body>
</html>