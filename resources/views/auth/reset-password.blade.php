<x-guest-layout>
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .password-input-wrapper {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            font-size: 16px;
            cursor: pointer;
            transition: color 0.3s ease;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: #4f46e5;
        }
    </style>
    
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__("Email")" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__("Password")" />
            <div class="password-input-wrapper">
                <x-text-input id="password" class="block mt-1 w-full pr-12" type="password" name="password" required autocomplete="new-password" />
                <i class="fas fa-eye password-toggle" onclick="togglePasswordVisibility('password')" id="togglePassword"></i>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__("Confirm Password")" />
            <div class="password-input-wrapper">
                <x-text-input id="password_confirmation" class="block mt-1 w-full pr-12"
                                    type="password"
                                    name="password_confirmation" required autocomplete="new-password" />
                <i class="fas fa-eye password-toggle" onclick="togglePasswordVisibility('password_confirmation')" id="togglePasswordConfirmation"></i>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __("Reset Password") }}
            </x-primary-button>
        </div>
    </form>
    
    <script>
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
</x-guest-layout>
