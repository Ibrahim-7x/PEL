<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'PEL Project')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    
    <!-- Page-specific meta tags -->
    @yield('meta')
</head>

<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div id="sidebar" class="bg-light border-end vh-100 p-3 position-fixed top-0 start-0 d-flex flex-column overflow-hidden"
         style="width: 250px; transition: width 0.3s;">
        
        <!-- Logo + Text -->
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo abacus-1.png') }}" 
                 alt="PEL Logo" 
                 id="sidebarLogo"
                 class="img-fluid mb-2"
                 style="width:100px; height:100px; object-fit:contain; transition: all 0.3s ease;">
 
            <h5 class="fw-bold text-dark mb-1 sidebar-text">PEL-Abacus</h5>
            <hr class="mt-3 mb-0 sidebar-text" style="border-top: 2px solid #ddd; width: 80%; margin: 0 auto;">
        </div>

        <!-- Sidebar Menu -->
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->is('profile') ? 'active fw-bold' : '' }}" href="{{ url('/profile') }}">
                    🏠 <span class="sidebar-text">Home</span>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->is('home-agent') || request()->is('home-management') ? 'active fw-bold' : '' }}" 
                   href="{{ auth()->check() ? (auth()->user()->role === 'Agent' ? url('/home-agent') : url('/home-management')) : route('login') }}">
                    📄 <span class="sidebar-text">RU Case Form</span>
                </a>
            </li>
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
</style>

<!-- Page-specific JavaScript -->
@yield('scripts')

</body>
</html>