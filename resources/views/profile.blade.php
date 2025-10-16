@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<style>
    .profile-hero {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        min-height: 40vh;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        margin: -2rem -2rem 3rem -2rem;
        position: relative;
        overflow: hidden;
    }

    .profile-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid rgba(123, 143, 161, 0.3);
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        transition: all 0.3s ease;
    }

    .profile-avatar:hover {
        transform: scale(1.05);
        box-shadow: 0 12px 40px rgba(0,0,0,0.4);
    }

    .profile-name {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 1rem 0;
        text-shadow: 0 4px 8px rgba(0,0,0,0.3);
        background: linear-gradient(45deg, #7b8fa1, #5a6c7d);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .profile-role {
        font-size: 1.1rem;
        opacity: 0.9;
        background: rgba(123, 143, 161, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        display: inline-block;
    }

    .profile-content {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 3rem;
        margin-top: -2rem;
        position: relative;
        z-index: 2;
    }

    .info-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid rgba(123, 143, 161, 0.1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        animation: fadeInUp 0.8s ease-out;
        animation-fill-mode: both;
    }

    .info-card:nth-child(1) { animation-delay: 0.2s; }
    .info-card:nth-child(2) { animation-delay: 0.4s; }

    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(123, 143, 161, 0.2);
    }

    .info-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(45deg, #7b8fa1, #5a6c7d);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 2rem;
    }

    .info-title {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .password-section {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin-top: 2rem;
        border: 1px solid rgba(123, 143, 161, 0.1);
        animation: fadeInUp 0.8s ease-out;
        animation-delay: 0.6s;
        animation-fill-mode: both;
    }

    .password-form .form-label {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .password-form .form-control {
        border: 2px solid rgba(123, 143, 161, 0.2);
        border-radius: 10px;
        padding: 0.8rem 1rem;
        transition: all 0.3s ease;
        background: rgba(123, 143, 161, 0.05);
    }

    .password-form .form-control:focus {
        border-color: #7b8fa1;
        box-shadow: 0 0 0 0.2rem rgba(123, 143, 161, 0.25);
        background: rgba(123, 143, 161, 0.08);
        transform: scale(1.02);
    }

    .btn-update-password {
        background: linear-gradient(45deg, #7b8fa1, #5a6c7d);
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 25px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(123, 143, 161, 0.3);
    }

    .btn-update-password:hover {
        background: linear-gradient(45deg, #5a6c7d, #4a5d6a);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(123, 143, 161, 0.4);
        color: white;
    }

    .password-strength {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .password-strength.weak { color: #dc3545; }
    .password-strength.medium { color: #ffc107; }
    .password-strength.strong { color: #28a745; }

    .logout-section {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        margin-top: 2rem;
        border: 1px solid rgba(220, 53, 69, 0.1);
    }

    .btn-logout {
        background: linear-gradient(45deg, #dc3545, #c82333);
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 25px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    }

    .btn-logout:hover {
        background: linear-gradient(45deg, #c82333, #bd2130);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        color: white;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .profile-name {
            font-size: 2rem;
        }
        .profile-hero {
            min-height: 30vh;
        }
    }
</style>

<div class="profile-hero">
    <div class="container">
        <div class="text-center">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=7b8fa1&color=fff&size=120"
                 alt="Avatar"
                 class="profile-avatar mb-3">
            <h1 class="profile-name">{{ Auth::user()->name }}</h1>
            <div class="profile-role">{{ Auth::user()->role }}</div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="profile-content">
                <h3 class="text-center mb-4" style="color: #2c3e50; font-weight: 700;">Profile Information</h3>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="bi bi-person-circle"></i>
                            </div>
                            <div class="info-title">Full Name</div>
                            <div class="info-value">{{ Auth::user()->name }}</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div class="info-title">Username</div>
                            <div class="info-value">{{ Auth::user()->username }}</div>
                        </div>
                    </div>
                </div>

                <!-- Password Change Section -->
                <div class="password-section">
                    <h5 class="mb-3" style="color: #2c3e50; font-weight: 700;">
                        <i class="bi bi-shield-lock me-2"></i>Change Password
                    </h5>
                    <p class="text-muted mb-4">Update your password to keep your account secure.</p>

                    <!-- Success/Error Messages -->
                    <div id="password-success" class="alert alert-success alert-dismissible fade show" style="display: none;" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        <span id="success-message"></span>
                        <button type="button" class="btn-close" onclick="hidePasswordSuccess()"></button>
                    </div>

                    <div id="password-errors" class="alert alert-danger" style="display: none;" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <div id="error-messages"></div>
                    </div>

                    <form id="passwordForm" class="password-form">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="current_password" class="form-label">
                                    <i class="bi bi-key me-2"></i>Current Password
                                </label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-2"></i>New Password
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div id="password-strength" class="password-strength"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">
                                    <i class="bi bi-lock-fill me-2"></i>Confirm New Password
                                </label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                <div id="password-match" class="password-strength" style="display: none;"></div>
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="showPasswords" onchange="togglePasswordVisibility()">
                                    <label class="form-check-label" for="showPasswords">
                                        Show passwords
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-update-password" id="updatePasswordBtn">
                                <i class="bi bi-shield-check me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>

                <div class="logout-section">
                    <h5 class="mb-3" style="color: #dc3545;">Account Actions</h5>
                    <p class="text-muted mb-4">Need to sign out? Click the button below to securely log out of your account.</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-logout">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordForm = document.getElementById('passwordForm');
    const updatePasswordBtn = document.getElementById('updatePasswordBtn');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const passwordStrength = document.getElementById('password-strength');
    const passwordMatch = document.getElementById('password-match');

    // Password strength checker
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        let feedback = [];

        if (password.length >= 8) {
            strength++;
        } else {
            feedback.push('At least 8 characters');
        }

        if (/[a-z]/.test(password)) {
            strength++;
        } else {
            feedback.push('Lowercase letter');
        }

        if (/[A-Z]/.test(password)) {
            strength++;
        } else {
            feedback.push('Uppercase letter');
        }

        if (/\d/.test(password)) {
            strength++;
        } else {
            feedback.push('Number');
        }

        if (/[^A-Za-z0-9]/.test(password)) {
            strength++;
        } else {
            feedback.push('Special character');
        }

        passwordStrength.className = 'password-strength';
        if (strength < 3) {
            passwordStrength.classList.add('weak');
            passwordStrength.textContent = 'Weak: ' + feedback.join(', ');
        } else if (strength < 5) {
            passwordStrength.classList.add('medium');
            passwordStrength.textContent = 'Medium: ' + feedback.join(', ');
        } else {
            passwordStrength.classList.add('strong');
            passwordStrength.textContent = 'Strong password!';
        }
    });

    // Password confirmation checker
    confirmPasswordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirmPassword = this.value;

        if (confirmPassword.length > 0) {
            passwordMatch.style.display = 'block';
            if (password === confirmPassword) {
                passwordMatch.className = 'password-strength strong';
                passwordMatch.textContent = '✓ Passwords match';
            } else {
                passwordMatch.className = 'password-strength weak';
                passwordMatch.textContent = '✗ Passwords do not match';
            }
        } else {
            passwordMatch.style.display = 'none';
        }
    });

    // Form submission
    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = updatePasswordBtn;

        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Updating...';

        // Hide previous messages
        hidePasswordMessages();

        fetch('{{ route("profile.update-password") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showPasswordSuccess(data.message);
                passwordForm.reset();
                passwordStrength.textContent = '';
                passwordMatch.style.display = 'none';
            } else {
                showPasswordErrors(data.errors);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showPasswordErrors({'general': ['An error occurred. Please try again.']});
        })
        .finally(() => {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-shield-check me-2"></i>Update Password';
        });
    });
});

// Password visibility toggle
function togglePasswordVisibility() {
    const showPasswords = document.getElementById('showPasswords');
    const passwordFields = [
        document.getElementById('current_password'),
        document.getElementById('password'),
        document.getElementById('password_confirmation')
    ];

    passwordFields.forEach(field => {
        field.type = showPasswords.checked ? 'text' : 'password';
    });
}

// Message display functions
function showPasswordSuccess(message) {
    const successDiv = document.getElementById('password-success');
    const successMessage = document.getElementById('success-message');
    successMessage.textContent = message;
    successDiv.style.display = 'block';
    successDiv.classList.add('show');
}

function hidePasswordSuccess() {
    const successDiv = document.getElementById('password-success');
    successDiv.style.display = 'none';
    successDiv.classList.remove('show');
}

function showPasswordErrors(errors) {
    const errorDiv = document.getElementById('password-errors');
    const errorMessages = document.getElementById('error-messages');
    errorMessages.innerHTML = '';

    if (errors.general) {
        errors.general.forEach(error => {
            errorMessages.innerHTML += `<div>${error}</div>`;
        });
    } else {
        Object.keys(errors).forEach(field => {
            errors[field].forEach(error => {
                errorMessages.innerHTML += `<div><strong>${field.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}:</strong> ${error}</div>`;
            });
        });
    }

    errorDiv.style.display = 'block';
}

function hidePasswordMessages() {
    document.getElementById('password-success').style.display = 'none';
    document.getElementById('password-errors').style.display = 'none';
}
</script>
@endsection
