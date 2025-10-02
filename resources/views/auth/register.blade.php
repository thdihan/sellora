<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create your account - Sellora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f5f5f7;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            width: 100%;
            max-width: 500px;
        }

        .register-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 48px 32px;
            border: 1px solid #e5e7eb;
        }

        .register-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .register-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .register-header p {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 0;
        }

        .register-header p a {
            color: #6366f1;
            text-decoration: none;
            font-weight: 500;
        }

        .register-header p a:hover {
            text-decoration: underline;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 16px;
            background-color: white;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            cursor: pointer;
            font-size: 16px;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: #374151;
        }

        .btn-register {
            width: 100%;
            background-color: #6366f1;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-bottom: 24px;
        }

        .btn-register:hover {
            background-color: #5856eb;
        }

        .btn-register:active {
            background-color: #4f46e5;
        }

        .divider {
            text-align: center;
            margin: 24px 0;
            position: relative;
            color: #6b7280;
            font-size: 14px;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: #e5e7eb;
            z-index: 1;
        }

        .divider span {
            background-color: white;
            padding: 0 16px;
            position: relative;
            z-index: 2;
        }

        .social-buttons {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        .social-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background-color: white;
            color: #374151;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.2s ease;
        }

        .social-btn:hover {
            background-color: #f9fafb;
            border-color: #9ca3af;
            color: #374151;
        }

        .social-btn i {
            font-size: 18px;
        }

        .login-link {
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }

        .login-link a {
            color: #6366f1;
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert i {
            margin-right: 8px;
        }

        /* Loading state */
        .btn-register.loading {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-register.loading::after {
            content: '';
            width: 16px;
            height: 16px;
            margin-left: 8px;
            border: 2px solid transparent;
            border-top-color: white;
            border-radius: 50%;
            display: inline-block;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 640px) {
            .register-card {
                padding: 32px 24px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .social-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h1>Create your account</h1>
                <p>Or <a href="{{ route('login') }}">sign in to your account</a></p>
            </div>

            @if ($errors->any())
                <div class="alert">
                    @foreach ($errors->all() as $error)
                        <div><i class="fas fa-exclamation-circle"></i>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required placeholder="Enter your full name">
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email address">
                    </div>
                </div>

                <div class="form-group">
                    <label for="employee_id" class="form-label">Employee ID</label>
                    <input type="text" class="form-control" id="employee_id" name="employee_id" value="{{ old('employee_id') }}" placeholder="Enter your employee ID">
                </div>

                <div class="form-group">
                    <label for="designation" class="form-label">Designation (Optional)</label>
                    <input type="text" class="form-control" id="designation" name="designation" value="{{ old('designation') }}" placeholder="e.g., Senior Sales Manager">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                            <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Confirm your password">
                            <i class="fas fa-eye password-toggle" onclick="togglePassword('password_confirmation')"></i>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-register" id="registerBtn">
                    Create Account
                </button>
            </form>

            <div class="divider">
                <span>Or continue with</span>
            </div>

            <div class="social-buttons">
                <a href="#" class="social-btn" onclick="return false;">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-btn" onclick="return false;">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="social-btn" onclick="return false;">
                    <i class="fab fa-google"></i>
                </a>
            </div>

            <div class="login-link">
                Already have an account? <a href="{{ route('login') }}">Sign in here</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = passwordField.nextElementSibling;
            
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

        // Form submission loading state
        document.getElementById('registerForm').addEventListener('submit', function() {
            const btn = document.getElementById('registerBtn');
            btn.classList.add('loading');
            btn.textContent = 'Creating Account...';
        });
    </script>
</body>
</html>
