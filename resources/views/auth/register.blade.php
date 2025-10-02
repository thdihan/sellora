<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sellora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e6f2eb 0%, #d0e8d2 50%, #b8dfc2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .register-header {
            background: linear-gradient(135deg, #7fb47f 0%, #6ba46b 50%, #5a9a5a 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .register-body {
            padding: 2rem;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #7fb47f;
            box-shadow: 0 0 0 0.2rem rgba(127, 180, 127, 0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #7fb47f 0%, #6ba46b 50%, #5a9a5a 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-register:hover {
            transform: translateY(-2px);
        }
        .password-input-wrapper {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 16px;
            cursor: pointer;
            transition: color 0.3s ease;
            z-index: 10;
        }
        .password-toggle:hover {
            color: #7fb47f;
            box-shadow: 0 5px 15px rgba(127, 180, 127, 0.4);
        }
        .register-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            margin: 0;
        }
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="register-card">
                    <div class="register-header">
                        <div class="logo-container mb-3">
                            <img src="{{ asset('assets/brand/sellora-logo.png') }}" alt="Sellora Logo" class="mx-auto" width="80" height="80" style="border-radius: 15px;">
                        </div>
                        <h2 class="mb-0">Join Sellora</h2>
                        <p class="mb-0 mt-2">Create your account</p>
                    </div>
                    <div class="register-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="password-input-wrapper">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <i class="fas fa-eye password-toggle" id="togglePassword" onclick="togglePasswordVisibility('password')"></i>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <div class="password-input-wrapper">
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                        <i class="fas fa-eye password-toggle" id="togglePasswordConfirmation" onclick="togglePasswordVisibility('password_confirmation')"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">Employee ID</label>
                                <input type="text" class="form-control" id="employee_id" name="employee_id" value="{{ old('employee_id') }}" placeholder="Enter your employee ID">
                            </div>
                            
                            <div class="mb-4">
                                <label for="designation" class="form-label">Designation (Optional)</label>
                                <input type="text" class="form-control" id="designation" name="designation" value="{{ old('designation') }}" placeholder="e.g., Senior Sales Manager">
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-register w-100 mb-3">Create Account</button>
                        </form>
                        
                        <div class="text-center">
                            <p class="mb-0">Already have an account? <a href="{{ route('login') }}" class="text-decoration-none">Sign in here</a></p>
                        </div>
                    </div>
                    <div class="register-footer">
                        <div class="text-center py-3">
                            <small class="text-muted">
                                 &copy; {{ date('Y') }} Sellora. All rights reserved.<br>
                                 Developed by <a href="https://www.webnexa.eporichoy.com" target="_blank" class="text-decoration-none" style="color: #7fb47f;">WebNexa</a> 
                                 a Concern of <a href="https://www.eporichoy.com" target="_blank" class="text-decoration-none" style="color: #7fb47f;">E-Porichoy</a>
                             </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle function
        function togglePasswordVisibility(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = document.querySelector(`#toggle${fieldId.charAt(0).toUpperCase() + fieldId.slice(1).replace('_', '')}`);
            
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
