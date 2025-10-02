<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sellora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e6f2eb 0%, #d0e8d2 50%, #b8dfc2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .reset-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .reset-header {
            background: linear-gradient(135deg, #7fb47f 0%, #6ba46b 50%, #5a9a5a 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .reset-body {
            padding: 2rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #7fb47f;
            box-shadow: 0 0 0 0.2rem rgba(127, 180, 127, 0.25);
        }
        .btn-reset {
            background: linear-gradient(135deg, #7fb47f 0%, #6ba46b 50%, #5a9a5a 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(127, 180, 127, 0.4);
        }
        .reset-footer {
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
            <div class="col-md-6 col-lg-5">
                <div class="reset-card">
                    <div class="reset-header">
                        <div class="logo-container mb-3">
                            <img src="{{ asset('assets/brand/sellora-logo.png') }}" alt="Sellora Logo" class="mx-auto" width="80" height="80" style="border-radius: 15px;">
                        </div>
                        <h2 class="mb-0">Reset Password</h2>
                        <p class="mb-0 mt-2">Enter your email address and we'll send you a password reset link</p>
                    </div>
                    <div class="reset-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
                            </div>
                            <button type="submit" class="btn btn-primary btn-reset w-100 mb-3">Send Password Reset Link</button>
                        </form>
                        
                        <div class="text-center">
                            <p class="mb-0">Remember your password? <a href="{{ route('login') }}" class="text-decoration-none" style="color: #7fb47f;">Back to login</a></p>
                        </div>
                    </div>
                    <div class="reset-footer">
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
</body>
</html>
