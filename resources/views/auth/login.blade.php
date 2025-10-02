<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sellora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #e6f2eb 0%, #d0e8d2 50%, #b8dfc2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Floating particles animation */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: rgba(127, 180, 127, 0.3);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .particle:nth-child(1) { width: 10px; height: 10px; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 15px; height: 15px; left: 20%; animation-delay: 1s; }
        .particle:nth-child(3) { width: 8px; height: 8px; left: 30%; animation-delay: 2s; }
        .particle:nth-child(4) { width: 12px; height: 12px; left: 40%; animation-delay: 3s; }
        .particle:nth-child(5) { width: 18px; height: 18px; left: 50%; animation-delay: 4s; }
        .particle:nth-child(6) { width: 14px; height: 14px; left: 60%; animation-delay: 5s; }
        .particle:nth-child(7) { width: 9px; height: 9px; left: 70%; animation-delay: 0.5s; }
        .particle:nth-child(8) { width: 16px; height: 16px; left: 80%; animation-delay: 1.5s; }
        .particle:nth-child(9) { width: 11px; height: 11px; left: 90%; animation-delay: 2.5s; }

        @keyframes float {
            0%, 100% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
        }

        .container {
            position: relative;
            z-index: 2;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transform: translateY(50px);
            opacity: 0;
            animation: slideInUp 1s ease-out forwards;
            border: 1px solid rgba(127, 180, 127, 0.2);
        }

        @keyframes slideInUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .login-header {
            background: linear-gradient(135deg, #7fb47f 0%, #6ba46b 50%, #5a9a5a 100%);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
            100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1rem;
        }

        .logo-container img {
            border-radius: 20px;
            animation: logoFloat 3s ease-in-out infinite;
            transition: transform 0.3s ease;
        }

        .logo-container img:hover {
            transform: scale(1.1) rotate(5deg);
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .login-header h2 {
            margin-bottom: 0.5rem;
            font-weight: 700;
            letter-spacing: 1px;
            animation: fadeInDown 1s ease-out 0.5s both;
        }

        .login-header p {
            margin-bottom: 0;
            opacity: 0.9;
            animation: fadeInDown 1s ease-out 0.7s both;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-body {
            padding: 2.5rem;
            animation: fadeInUp 1s ease-out 0.3s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            position: relative;
            margin-bottom: 2rem;
        }

        .form-control {
            border-radius: 15px;
            border: 2px solid #e9ecef;
            padding: 15px 20px 15px 50px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: #7fb47f;
            box-shadow: 0 0 0 0.3rem rgba(127, 180, 127, 0.25);
            transform: translateY(-2px);
            background: white;
        }

        .form-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #7fb47f;
            font-size: 18px;
        }

        .password-input-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 18px;
            cursor: pointer;
            transition: color 0.3s ease;
            z-index: 10;
        }

        .password-toggle:hover {
            color: #7fb47f;
            transition: all 0.3s ease;
        }

        .form-control:focus + .form-icon {
            color: #5a9a5a;
            transform: translateY(-50%) scale(1.1);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            transition: color 0.3s ease;
        }

        .btn-login {
            background: linear-gradient(135deg, #7fb47f 0%, #6ba46b 50%, #5a9a5a 100%);
            border: none;
            border-radius: 15px;
            padding: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 16px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(127, 180, 127, 0.4);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 15px;
            border: none;
            animation: slideInDown 0.5s ease-out;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-footer {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-top: 1px solid rgba(127, 180, 127, 0.2);
            margin: 0;
            animation: fadeIn 1s ease-out 1s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .text-link {
            color: #7fb47f;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .text-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: #7fb47f;
            transition: width 0.3s ease;
        }

        .text-link:hover {
            color: #5a9a5a;
            transform: translateY(-1px);
        }

        .text-link:hover::after {
            width: 100%;
        }

        /* Loading animation for form submission */
        .btn-login.loading {
            pointer-events: none;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .login-card {
                margin: 1rem;
            }
            
            .login-header {
                padding: 2rem 1.5rem;
            }
            
            .login-body {
                padding: 2rem 1.5rem;
            }
        }

        /* Input animation effects */
        .form-control:not(:placeholder-shown) + .form-icon {
            color: #5a9a5a;
        }

        .form-group.focused .form-label {
            color: #7fb47f;
        }
    </style>
</head>
<body>
    <!-- Floating particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                    <div class="login-header">
                        <div class="logo-container">
                            <img src="{{ asset('assets/brand/sellora-logo.png') }}" alt="Sellora Logo" width="80" height="80">
                        </div>
                        <h2>Welcome to Sellora</h2>
                        <p>Sign in to your account</p>
                    </div>
                    
                    <div class="login-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" id="loginForm">
                            @csrf
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email">
                                <i class="fas fa-envelope form-icon"></i>
                            </div>
                            
                            <div class="form-group">
                                <label for="password" class="form-label">Password</label>
                                <div class="password-input-wrapper">
                                    <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                                    <i class="fas fa-lock form-icon"></i>
                                    <i class="fas fa-eye password-toggle" id="togglePassword" onclick="togglePasswordVisibility('password')"></i>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
                                <span class="btn-text">Sign In</span>
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <p class="mb-2">
                                <a href="{{ route('password.request') }}" class="text-link">
                                    <i class="fas fa-key me-1"></i>Forgot your password?
                                </a>
                            </p>
                            <p class="mb-0">
                                Don't have an account? 
                                <a href="{{ route('register') }}" class="text-link">
                                    <i class="fas fa-user-plus me-1"></i>Sign up here
                                </a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="login-footer">
                        <div class="text-center py-3">
                            <small class="text-muted">
                                <i class="fas fa-copyright me-1"></i>{{ date('Y') }} Sellora. All rights reserved.<br>
                                Developed by <a href="https://www.webnexa.eporichoy.com" target="_blank" class="text-link">WebNexa</a> 
                                a Concern of <a href="https://www.eporichoy.com" target="_blank" class="text-link">E-Porichoy</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form interaction animations
        document.addEventListener('DOMContentLoaded', function() {
            const formControls = document.querySelectorAll('.form-control');
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.querySelector('.btn-login');

            // Add focus/blur animations to form inputs
            formControls.forEach(input => {
                input.addEventListener('focus', function() {
                    this.closest('.form-group').classList.add('focused');
                });

                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.closest('.form-group').classList.remove('focused');
                    }
                });

                // Check if input has value on page load
                if (input.value) {
                    input.closest('.form-group').classList.add('focused');
                }
            });

            // Form submission animation
            loginForm.addEventListener('submit', function(e) {
                loginBtn.classList.add('loading');
                loginBtn.querySelector('.btn-text').textContent = 'Signing In...';
            });

            // Add ripple effect to button
            loginBtn.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Add ripple effect CSS
        const style = document.createElement('style');
        style.textContent = `
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                pointer-events: none;
            }
            
            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Password visibility toggle function
        function togglePasswordVisibility(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = document.querySelector(`#toggle${fieldId.charAt(0).toUpperCase() + fieldId.slice(1)}`);
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
