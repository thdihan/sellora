<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" style="background: linear-gradient(135deg, #e6f2eb 0%, #d0e8d2 50%, #b8dfc2 100%);">
            <div class="mb-6">
                <a href="/">
                    <img src="{{ asset('assets/brand/sellora-logo.png') }}" alt="Sellora Logo" class="mx-auto" width="80" height="80" style="border-radius: 15px;">
                </a>
            </div>

            <div class="w-full sm:max-w-md bg-white shadow-md overflow-hidden sm:rounded-lg">
                <div class="px-6 py-4">
                    @yield('content')
                </div>
                <div class="px-6 py-3 border-t" style="background-color: #f0f8f0; border-color: #d0e8d2;">
                    <div class="text-center mt-4">
                      <small class="text-gray-500">
                          &copy; {{ date('Y') }} Sellora. All rights reserved.<br>
                          Developed by <a href="https://www.webnexa.eporichoy.com" target="_blank" class="text-decoration-none" style="color: #7fb47f;">WebNexa</a> 
                          a Concern of <a href="https://www.eporichoy.com" target="_blank" class="text-decoration-none" style="color: #7fb47f;">E-Porichoy</a>
                      </small>
                  </div>
                </div>
            </div>
        </div>
    </body>
</html>
