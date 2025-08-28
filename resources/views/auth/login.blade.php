<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PEL-Abacus</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #7b8fa1;
            --secondary-color: #5a6c7d;
            --accent-color: #4a5d6a;
            --dark-color: #2c3e50;
            --text-color: #ffffff;
            --muted-text: rgba(255, 255, 255, 0.6);
        }
        
        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            margin: 0;
            background: linear-gradient(135deg, var(--dark-color) 0%, #34495e 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
            color: var(--text-color);
        }
        
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        
        .container {
            position: relative;
            z-index: 2;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            animation: cardEntrance 1s ease-out;
            transform-style: preserve-3d;
            perspective: 1000px;
            position: relative;
            max-width: 450px;
            width: 100%;
            overflow: hidden;
            padding: 3rem 2rem;
        }
        
        .login-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            border-radius: 22px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color), var(--accent-color), var(--secondary-color));
            background-size: 400% 400%;
            z-index: -1;
            animation: borderGlow 6s linear infinite;
            filter: blur(10px);
        }
        
        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes borderGlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .logo {
            width: 120px;
            height: auto;
            filter: drop-shadow(0 0 10px rgba(123, 143, 161, 0.6));
        }
        
        h2 {
            color: var(--text-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
            animation: textGlow 3s infinite;
        }
        
        @keyframes textGlow {
            0% { text-shadow: 0 0 5px rgba(123, 143, 161, 0.5); }
            50% { text-shadow: 0 0 20px rgba(123, 143, 161, 0.8); }
            100% { text-shadow: 0 0 5px rgba(123, 143, 161, 0.5); }
        }
        
        .text-muted {
            color: var(--muted-text) !important;
            margin-bottom: 2rem;
        }
        
        .form-floating {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .form-floating .form-control {
            height: 60px;
            padding: 1rem 1rem 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
        }
        
        .form-floating .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--primary-color);
            box-shadow: 0 0 15px rgba(123, 143, 161, 0.4);
            outline: none;
        }
        
        .form-floating label {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            padding: 1rem 1rem;
            pointer-events: none;
            transform-origin: 0 0;
            transition: all 0.25s ease;
            color: var(--muted-text);
        }
        
        .form-floating .form-control:focus ~ label,
        .form-floating .form-control:not(:placeholder-shown) ~ label {
            transform: translateY(-0.5rem) translateX(0.25rem) scale(0.85);
            color: var(--primary-color);
        }
        
        .input-focus-effect {
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .form-control:focus ~ .input-focus-effect {
            width: 100%;
        }
        
        .btn-login {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            font-weight: 500;
            font-size: 1.1rem;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            overflow: hidden;
            position: relative;
            z-index: 1;
            transition: all 0.4s ease;
            box-shadow: 0 4px 15px rgba(123, 143, 161, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(123, 143, 161, 0.4);
        }
        
        .btn-login::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, var(--accent-color), var(--secondary-color));
            z-index: -1;
            transition: all 0.4s ease;
        }
        
        .btn-login:hover::after {
            left: 0;
        }
        
        .btn-login .spinner-border {
            display: none;
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
        }
        
        .btn-login.loading {
            background: var(--secondary-color);
            pointer-events: none;
        }
        
        .btn-login.loading .spinner-border {
            display: inline-block;
        }
        
        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            border-left: 4px solid #28a745;
            color: #d4edda;
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            border-left: 4px solid #dc3545;
            color: #f8d7da;
        }
        
        .alert .close {
            position: absolute;
            right: 1rem;
            top: 1rem;
            background: transparent;
            border: none;
            color: inherit;
            font-size: 1.2rem;
            opacity: 0.5;
            transition: opacity 0.3s;
        }
        
        .alert .close:hover {
            opacity: 1;
        }
        
        .footer {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 1rem;
            color: var(--muted-text);
            font-size: 0.9rem;
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .login-card {
                padding: 2rem 1.5rem;
            }
            
            .logo {
                width: 100px;
            }
        }
        
        @media (max-width: 576px) {
            .login-card {
                padding: 1.5rem 1rem;
            }
            
            .logo {
                width: 80px;
            }
            
            h2 {
                font-size: 1.5rem;
            }
            
            .btn-login {
                font-size: 1rem;
                padding: 0.7rem 1.5rem;
            }
        }
        
        @media (max-height: 700px) {
            .container {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
            
            .logo-container {
                margin-bottom: 1rem !important;
            }
            
            .form-floating {
                margin-bottom: 1rem;
            }
            
            .form-floating .form-control {
                height: 50px;
            }
        }
    </style>
</head>
<body>
    <!-- Particles Background -->
    <div id="particles-js"></div>
    
    <div class="container">
        <div class="col-12">
            <div class="login-card mx-auto">
                <div class="logo-container">
                    <img src="{{ asset('images/logo abacus-1.png') }}" alt="PEL Abacus Logo" class="logo">
                    <h2 class="mt-3">PEL-ABACUS</h2>
                    <p class="text-muted">Access your account</p>
                </div>
                
                @if (session('success'))
                    <div class="alert alert-success" id="success-alert">
                        <button class="close" onclick="document.getElementById('success-alert').style.display='none'">&times;</button>
                        ✅ {{ session('success') }}
                    </div>
                @endif
                
                @if ($errors->any())
                    <div class="alert alert-danger" id="error-alert">
                        <button class="close" onclick="document.getElementById('error-alert').style.display='none'">&times;</button>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>❌ {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                        <label for="username">Username</label>
                        <div class="input-focus-effect"></div>
                    </div>
                    
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                        <div class="input-focus-effect"></div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-login" id="loginButton">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p>&copy; {{ date('Y') }} PEL-Abacus. All rights reserved.</p>
    </footer>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        // Initialize particles
        particlesJS('particles-js', {
            particles: {
                number: {
                    value: 80,
                    density: { enable: true, value_area: 800 }
                },
                color: { value: "#ffffff" },
                shape: {
                    type: "circle",
                },
                opacity: {
                    value: 0.5,
                    random: true,
                },
                size: {
                    value: 3,
                    random: true,
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: "#7b8fa1",
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: "none",
                    random: false,
                    straight: false,
                    out_mode: "out",
                    bounce: false,
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: { enable: true, mode: "repulse" },
                    onclick: { enable: true, mode: "push" },
                    resize: true
                },
            },
            retina_detect: true
        });
        
        // Form submit animation
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');
            
            loginForm.addEventListener('submit', function() {
                loginButton.classList.add('loading');
                loginButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Logging in...';
            });
            
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.display = 'none';
                });
            }, 5000);
        });
    </script>
</body>
</html>