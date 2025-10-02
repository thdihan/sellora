@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@if(session('error'))
<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-6" role="alert">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

<!-- Stats Cards -->
<div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4 mb-8">
    <!-- Customers Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-3">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg">
                        <i class="fas fa-users text-white text-lg"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ number_format($dashboardCards['customers']['count']) }}</h3>
                <p class="text-gray-600 text-sm font-medium">Total Customers</p>
            </div>
            <div class="text-right">
                <div class="flex items-center {{ $dashboardCards['customers']['growth'] >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm font-semibold">
                    <i class="fas {{ $dashboardCards['customers']['growth'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} text-xs mr-1"></i>
                    {{ abs($dashboardCards['customers']['growth']) }}%
                </div>
                <p class="text-xs text-gray-500 mt-1">vs last month</p>
            </div>
        </div>
    </div>

    <!-- Orders Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-3">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center shadow-lg">
                        <i class="fas fa-shopping-cart text-white text-lg"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ number_format($dashboardCards['orders']['count']) }}</h3>
                <p class="text-gray-600 text-sm font-medium">Total Orders</p>
            </div>
            <div class="text-right">
                <div class="flex items-center {{ $dashboardCards['orders']['growth'] >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm font-semibold">
                    <i class="fas {{ $dashboardCards['orders']['growth'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} text-xs mr-1"></i>
                    {{ abs($dashboardCards['orders']['growth']) }}%
                </div>
                <p class="text-xs text-gray-500 mt-1">vs last month</p>
            </div>
        </div>
    </div>

    <!-- Monthly Target Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-3">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                        <i class="fas fa-bullseye text-white text-lg"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ number_format($dashboardCards['monthly_target']['percentage'], 2) }}%</h3>
                <p class="text-gray-600 text-sm font-medium">Monthly Target</p>
            </div>
            <div class="text-right">
                <div class="flex items-center {{ $dashboardCards['monthly_target']['growth'] >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm font-semibold">
                    <i class="fas {{ $dashboardCards['monthly_target']['growth'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} text-xs mr-1"></i>
                    {{ abs($dashboardCards['monthly_target']['growth']) }}%
                </div>
                <p class="text-xs text-gray-500 mt-1">vs last month</p>
            </div>
        </div>
        <!-- Progress Bar -->
        <div class="mt-4">
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full" style="width: {{ min($dashboardCards['monthly_target']['percentage'], 100) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Revenue Card -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-3">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center shadow-lg">
                        <i class="fas fa-dollar-sign text-white text-lg"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-1">${{ number_format($dashboardCards['revenue']['amount'], 2) }}</h3>
                <p class="text-gray-600 text-sm font-medium">Total Revenue</p>
            </div>
            <div class="text-right">
                <div class="flex items-center {{ $dashboardCards['revenue']['growth'] >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm font-semibold">
                    <i class="fas {{ $dashboardCards['revenue']['growth'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} text-xs mr-1"></i>
                    {{ abs($dashboardCards['revenue']['growth']) }}%
                </div>
                <p class="text-xs text-gray-500 mt-1">vs last month</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 gap-4 md:gap-6 2xl:gap-7.5 mb-6">
    <!-- Monthly Sales Chart -->
    <div class="col-span-12 xl:col-span-8">
        <div class="rounded-sm border border-stroke bg-white px-5 pt-7.5 pb-5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
            <div class="flex flex-wrap items-start justify-between gap-3 sm:flex-nowrap">
                <div class="flex w-full flex-wrap gap-3 sm:gap-5">
                    <div class="flex min-w-47.5">
                        <span class="mt-1 mr-2 flex h-4 w-full max-w-4 items-center justify-center rounded-full border border-primary">
                            <span class="block h-2.5 w-full max-w-2.5 rounded-full bg-primary"></span>
                        </span>
                        <div class="w-full">
                            <p class="font-semibold text-primary">Monthly Sales</p>
                            <p class="text-sm font-medium">12.04.2022 - 12.05.2022</p>
                        </div>
                    </div>
                </div>
                <div class="flex w-full max-w-45 justify-end">
                    <div class="inline-flex items-center rounded-md bg-whiter p-1.5 dark:bg-meta-4">
                        <button class="rounded bg-white py-1 px-3 text-xs font-medium text-black shadow-card hover:bg-white hover:shadow-card dark:bg-boxdark dark:text-white dark:hover:bg-boxdark">
                            Day
                        </button>
                        <button class="rounded py-1 px-3 text-xs font-medium text-black hover:bg-white hover:shadow-card dark:text-white dark:hover:bg-boxdark">
                            Week
                        </button>
                        <button class="rounded py-1 px-3 text-xs font-medium text-black hover:bg-white hover:shadow-card dark:text-white dark:hover:bg-boxdark">
                            Month
                        </button>
                    </div>
                </div>
            </div>
            <div>
                <div id="chartOne" class="-ml-5"></div>
            </div>
        </div>
    </div>

    <!-- Monthly Target Progress -->
    <div class="col-span-12 xl:col-span-4">
        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <h4 class="mb-6 text-xl font-semibold text-black dark:text-white">
                Monthly Target
            </h4>
            <div class="mb-2">
                <p class="flex justify-between text-sm font-medium text-black dark:text-white">
                    <span> Target you've set for this month </span>
                    <span> 75.55% </span>
                </p>
            </div>
            <div class="mb-9">
                <div class="relative h-2.5 w-full rounded-full bg-stroke dark:bg-strokedark">
                    <div class="absolute left-0 h-2.5 w-3/4 rounded-full bg-primary"></div>
                </div>
            </div>
            <div class="flex items-center justify-center">
                <div class="relative">
                    <svg class="rotate-90 transform" width="150" height="150">
                        <circle cx="75" cy="75" r="60" stroke="#E5E7EB" stroke-width="8" fill="none" />
                        <circle cx="75" cy="75" r="60" stroke="#3C50E0" stroke-width="8" fill="none" 
                                stroke-dasharray="377" stroke-dashoffset="85" stroke-linecap="round" />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <span class="text-2xl font-bold text-black dark:text-white">75.55%</span>
                            <p class="text-sm text-gray-500">Target</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-7.5 grid grid-cols-2 gap-4">
                <div class="text-center">
                    <p class="mb-1.5 text-sm font-medium text-black dark:text-white">৳30k</p>
                    <p class="text-xs">You gain today</p>
                </div>
                <div class="text-center">
                    <p class="mb-1.5 text-sm font-medium text-black dark:text-white">৳20k</p>
                    <p class="text-xs">You gain this month</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics and Recent Orders Section -->
<div class="grid grid-cols-1 gap-4 md:gap-6 2xl:gap-7.5 mb-6">
    <!-- Statistics Chart -->
    <div class="col-span-12 xl:col-span-4">
        <div class="rounded-sm border border-stroke bg-white px-5 pt-7.5 pb-5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5">
            <div class="mb-3 justify-between gap-4 sm:flex">
                <div>
                    <h5 class="text-xl font-semibold text-black dark:text-white">
                        Statistics
                    </h5>
                </div>
                <div>
                    <div class="relative z-20 inline-block">
                        <select name="" id="" class="relative z-20 inline-flex appearance-none bg-transparent py-1 pl-3 pr-8 text-sm font-medium outline-none">
                            <option value="">This Week</option>
                            <option value="">Last Week</option>
                        </select>
                        <span class="absolute top-1/2 right-3 z-10 -translate-y-1/2">
                            <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.47072 1.08816C0.47072 1.02932 0.500141 0.955772 0.54427 0.911642C0.647241 0.808672 0.809051 0.808672 0.912022 0.896932L4.85431 4.60386C4.92785 4.67741 5.15786 4.67741 5.23140 4.60386L9.17369 0.896932C9.27666 0.793962 9.43847 0.808672 9.54144 0.911642C9.6444 1.01461 9.6444 1.17642 9.54144 1.27939L5.50141 5.08816C5.22786 5.36171 4.80431 5.36171 4.53076 5.08816L0.47072 1.08816Z" fill="#637381"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
            <div>
                <div id="chartTwo" class="-ml-5 -mb-9"></div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-span-12 xl:col-span-8">
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
            <div class="mb-6 flex justify-between">
                <div>
                    <h4 class="text-xl font-semibold text-black dark:text-white">
                        Recent Orders
                    </h4>
                </div>
                <div>
                    <button class="flex items-center gap-2 rounded bg-primary py-2 px-4.5 font-medium text-white hover:bg-opacity-80">
                        <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 7H9V1C9 0.4 8.6 0 8 0C7.4 0 7 0.4 7 1V7H1C0.4 7 0 7.4 0 8C0 8.6 0.4 9 1 9H7V15C7 15.6 7.4 16 8 16C8.6 16 9 15.6 9 15V9H15C15.6 9 16 8.6 16 8C16 7.4 15.6 7 15 7Z" fill=""/>
                        </svg>
                        View All
                    </button>
                </div>
            </div>

            <div class="flex flex-col">
                <div class="grid grid-cols-3 rounded-sm bg-gray-2 dark:bg-meta-4 sm:grid-cols-5">
                    <div class="p-2.5 xl:p-5">
                        <h5 class="text-sm font-medium uppercase xsm:text-base">
                            Product
                        </h5>
                    </div>
                    <div class="p-2.5 text-center xl:p-5">
                        <h5 class="text-sm font-medium uppercase xsm:text-base">
                            Category
                        </h5>
                    </div>
                    <div class="p-2.5 text-center xl:p-5">
                        <h5 class="text-sm font-medium uppercase xsm:text-base">
                            Price
                        </h5>
                    </div>
                    <div class="hidden p-2.5 text-center sm:block xl:p-5">
                        <h5 class="text-sm font-medium uppercase xsm:text-base">
                            Status
                        </h5>
                    </div>
                    <div class="hidden p-2.5 text-center sm:block xl:p-5">
                        <h5 class="text-sm font-medium uppercase xsm:text-base">
                            Actions
                        </h5>
                    </div>
                </div>

                <div class="grid grid-cols-3 border-b border-stroke dark:border-strokedark sm:grid-cols-5">
                    <div class="flex items-center gap-3 p-2.5 xl:p-5">
                        <div class="flex-shrink-0">
                            <img src="https://via.placeholder.com/48x48/3C50E0/FFFFFF?text=IP" alt="Product" class="h-12 w-12 rounded-full" />
                        </div>
                        <p class="hidden text-black dark:text-white sm:block">
                            iPhone 15 Pro
                        </p>
                    </div>
                    <div class="flex items-center justify-center p-2.5 xl:p-5">
                        <p class="text-black dark:text-white">Electronics</p>
                    </div>
                    <div class="flex items-center justify-center p-2.5 xl:p-5">
                        <p class="text-meta-3">৳89,999</p>
                    </div>
                    <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                        <p class="inline-flex rounded-full bg-success bg-opacity-10 py-1 px-3 text-sm font-medium text-success">
                            Delivered
                        </p>
                    </div>
                    <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                        <button class="hover:text-primary">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.99981 14.8219C3.43106 14.8219 0.674805 9.50624 0.562305 9.28124C0.47793 9.11249 0.47793 8.88749 0.562305 8.71874C0.674805 8.49374 3.43106 3.20624 8.99981 3.20624C14.5686 3.20624 17.3248 8.49374 17.4373 8.71874C17.5217 8.88749 17.5217 9.11249 17.4373 9.28124C17.3248 9.50624 14.5686 14.8219 8.99981 14.8219ZM1.85605 8.99999C2.4748 10.0406 4.89356 13.5562 8.99981 13.5562C13.1061 13.5562 15.5248 10.0406 16.1436 8.99999C15.5248 7.95936 13.1061 4.44374 8.99981 4.44374C4.89356 4.44374 2.4748 7.95936 1.85605 8.99999Z" fill=""/>
                                <path d="M9 11.3906C7.67812 11.3906 6.60938 10.3219 6.60938 9C6.60938 7.67813 7.67812 6.60938 9 6.60938C10.3219 6.60938 11.3906 7.67813 11.3906 9C11.3906 10.3219 10.3219 11.3906 9 11.3906ZM9 7.875C8.38125 7.875 7.875 8.38125 7.875 9C7.875 9.61875 8.38125 10.125 9 10.125C9.61875 10.125 10.125 9.61875 10.125 9C10.125 8.38125 9.61875 7.875 9 7.875Z" fill=""/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-3 border-b border-stroke dark:border-strokedark sm:grid-cols-5">
                    <div class="flex items-center gap-3 p-2.5 xl:p-5">
                        <div class="flex-shrink-0">
                            <img src="https://via.placeholder.com/48x48/10B981/FFFFFF?text=MB" alt="Product" class="h-12 w-12 rounded-full" />
                        </div>
                        <p class="hidden text-black dark:text-white sm:block">
                            MacBook Pro
                        </p>
                    </div>
                    <div class="flex items-center justify-center p-2.5 xl:p-5">
                        <p class="text-black dark:text-white">Electronics</p>
                    </div>
                    <div class="flex items-center justify-center p-2.5 xl:p-5">
                        <p class="text-meta-3">৳189,999</p>
                    </div>
                    <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                        <p class="inline-flex rounded-full bg-warning bg-opacity-10 py-1 px-3 text-sm font-medium text-warning">
                            Pending
                        </p>
                    </div>
                    <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                        <button class="hover:text-primary">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.99981 14.8219C3.43106 14.8219 0.674805 9.50624 0.562305 9.28124C0.47793 9.11249 0.47793 8.88749 0.562305 8.71874C0.674805 8.49374 3.43106 3.20624 8.99981 3.20624C14.5686 3.20624 17.3248 8.49374 17.4373 8.71874C17.5217 8.88749 17.5217 9.11249 17.4373 9.28124C17.3248 9.50624 14.5686 14.8219 8.99981 14.8219ZM1.85605 8.99999C2.4748 10.0406 4.89356 13.5562 8.99981 13.5562C13.1061 13.5562 15.5248 10.0406 16.1436 8.99999C15.5248 7.95936 13.1061 4.44374 8.99981 4.44374C4.89356 4.44374 2.4748 7.95936 1.85605 8.99999Z" fill=""/>
                                <path d="M9 11.3906C7.67812 11.3906 6.60938 10.3219 6.60938 9C6.60938 7.67813 7.67812 6.60938 9 6.60938C10.3219 6.60938 11.3906 7.67813 11.3906 9C11.3906 10.3219 10.3219 11.3906 9 11.3906ZM9 7.875C8.38125 7.875 7.875 8.38125 7.875 9C7.875 9.61875 8.38125 10.125 9 10.125C9.61875 10.125 10.125 9.61875 10.125 9C10.125 8.38125 9.61875 7.875 9 7.875Z" fill=""/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-3 border-b border-stroke dark:border-strokedark sm:grid-cols-5">
                    <div class="flex items-center gap-3 p-2.5 xl:p-5">
                        <div class="flex-shrink-0">
                            <img src="https://via.placeholder.com/48x48/F59E0B/FFFFFF?text=AP" alt="Product" class="h-12 w-12 rounded-full" />
                        </div>
                        <p class="hidden text-black dark:text-white sm:block">
                            AirPods Pro
                        </p>
                    </div>
                    <div class="flex items-center justify-center p-2.5 xl:p-5">
                        <p class="text-black dark:text-white">Accessories</p>
                    </div>
                    <div class="flex items-center justify-center p-2.5 xl:p-5">
                        <p class="text-meta-3">৳24,999</p>
                    </div>
                    <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                        <p class="inline-flex rounded-full bg-danger bg-opacity-10 py-1 px-3 text-sm font-medium text-danger">
                            Cancelled
                        </p>
                    </div>
                    <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                        <button class="hover:text-primary">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.99981 14.8219C3.43106 14.8219 0.674805 9.50624 0.562305 9.28124C0.47793 9.11249 0.47793 8.88749 0.562305 8.71874C0.674805 8.49374 3.43106 3.20624 8.99981 3.20624C14.5686 3.20624 17.3248 8.49374 17.4373 8.71874C17.5217 8.88749 17.5217 9.11249 17.4373 9.28124C17.3248 9.50624 14.5686 14.8219 8.99981 14.8219ZM1.85605 8.99999C2.4748 10.0406 4.89356 13.5562 8.99981 13.5562C13.1061 13.5562 15.5248 10.0406 16.1436 8.99999C15.5248 7.95936 13.1061 4.44374 8.99981 4.44374C4.89356 4.44374 2.4748 7.95936 1.85605 8.99999Z" fill=""/>
                                <path d="M9 11.3906C7.67812 11.3906 6.60938 10.3219 6.60938 9C6.60938 7.67813 7.67812 6.60938 9 6.60938C10.3219 6.60938 11.3906 7.67813 11.3906 9C11.3906 10.3219 10.3219 11.3906 9 11.3906ZM9 7.875C8.38125 7.875 7.875 8.38125 7.875 9C7.875 9.61875 8.38125 10.125 9 10.125C9.61875 10.125 10.125 9.61875 10.125 9C10.125 8.38125 9.61875 7.875 9 7.875Z" fill=""/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                                    <div>
                                        <div class="fw-medium">Jane Smith</div>
                                        <small class="text-muted">jane@example.com</small>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">5 hours ago</small>
                                </td>
                                <td>
                                    <span class="fw-medium">৳1,89,999</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning-subtle text-warning">Processing</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded p-2 me-3">
                                            <i class="fas fa-headphones text-success"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium">AirPods Pro</div>
                                            <small class="text-muted">SKU: #9012</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-medium">Mike Johnson</div>
                                        <small class="text-muted">mike@example.com</small>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">1 day ago</small>
                                </td>
                                <td>
                                    <span class="fw-medium">৳24,999</span>
                                </td>
                                <td>
                                    <span class="badge bg-danger-subtle text-danger">Cancelled</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Demographics Section -->
<div class="grid grid-cols-1 gap-4 md:gap-6 2xl:gap-7.5 mb-6">
    <div class="col-span-12 xl:col-span-6">
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
            <div class="mb-6">
                <h4 class="text-xl font-semibold text-black dark:text-white">
                    Customer Demographics
                </h4>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="col-span-1">
                    <!-- World Map Placeholder -->
                    <div class="flex h-60 items-center justify-center rounded-sm bg-gray-2 dark:bg-meta-4">
                        <div class="text-center">
                            <svg class="mx-auto mb-3 h-12 w-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">World Map</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Customer distribution by location</p>
                        </div>
                    </div>
                </div>
                <div class="col-span-1">
                    <div class="flex h-full flex-col justify-center space-y-4">
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="mr-2 h-3 w-3 rounded-full bg-primary"></div>
                                    <span class="text-sm font-medium text-black dark:text-white">USA</span>
                                </div>
                                <span class="text-sm font-medium text-black dark:text-white">79%</span>
                            </div>
                            <div class="h-1 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                <div class="h-1 rounded-full bg-primary" style="width: 79%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="mr-2 h-3 w-3 rounded-full bg-secondary"></div>
                                    <span class="text-sm font-medium text-black dark:text-white">France</span>
                                </div>
                                <span class="text-sm font-medium text-black dark:text-white">23%</span>
                            </div>
                            <div class="h-1 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                <div class="h-1 rounded-full bg-secondary" style="width: 23%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-span-12 xl:col-span-6">
        <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
            <div class="mb-6">
                <h4 class="text-xl font-semibold text-black dark:text-white">
                    Quick Actions
                </h4>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <button class="flex flex-col items-center justify-center rounded-sm border border-stroke bg-gray-2 p-4 hover:bg-gray-3 dark:border-strokedark dark:bg-meta-4 dark:hover:bg-meta-4">
                    <svg class="mb-2 h-8 w-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span class="text-sm font-medium text-black dark:text-white">Create Order</span>
                </button>
                <button class="flex flex-col items-center justify-center rounded-sm border border-stroke bg-gray-2 p-4 hover:bg-gray-3 dark:border-strokedark dark:bg-meta-4 dark:hover:bg-meta-4">
                    <svg class="mb-2 h-8 w-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span class="text-sm font-medium text-black dark:text-white">View Reports</span>
                </button>
                <button class="flex flex-col items-center justify-center rounded-sm border border-stroke bg-gray-2 p-4 hover:bg-gray-3 dark:border-strokedark dark:bg-meta-4 dark:hover:bg-meta-4">
                    <svg class="mb-2 h-8 w-8 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-black dark:text-white">Add Expense</span>
                </button>
                <button class="flex flex-col items-center justify-center rounded-sm border border-stroke bg-gray-2 p-4 hover:bg-gray-3 dark:border-strokedark dark:bg-meta-4 dark:hover:bg-meta-4">
                    <svg class="mb-2 h-8 w-8 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-black dark:text-white">Pending Approvals</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stats-card {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        margin-bottom: 1rem;
        transition: transform 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card {
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border-radius: 10px;
    }

    .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        border-radius: 10px 10px 0 0 !important;
    }

    .progress {
        background-color: #e9ecef;
    }

    .progress-bar {
        background: linear-gradient(90deg, #28a745, #20c997);
    }

    /* Sales Target Styles */
    .target-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: 1px solid #e9ecef;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .target-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .target-header {
        border-bottom: 1px solid #f1f3f4;
        padding-bottom: 0.75rem;
        margin-bottom: 1rem;
    }

    .progress-section {
        margin-bottom: 1rem;
    }

    .progress-section:last-child {
        margin-bottom: 0;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// Monthly Sales Chart
const chartOneOptions = {
    series: [{
        name: 'Sales',
        data: [23, 11, 22, 27, 13, 22, 37, 21, 44, 22, 30, 45]
    }],
    chart: {
        type: 'bar',
        height: 335,
        toolbar: {
            show: false,
        },
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded',
            borderRadius: 2,
        },
    },
    dataLabels: {
        enabled: false,
    },
    stroke: {
        show: true,
        width: 4,
        colors: ['transparent'],
    },
    xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        axisBorder: {
            show: false,
        },
        axisTicks: {
            show: false,
        },
    },
    legend: {
        show: false,
    },
    grid: {
        strokeDashArray: 5,
        xaxis: {
            lines: {
                show: false,
            },
        },
        yaxis: {
            lines: {
                show: true,
            },
        },
    },
    fill: {
        opacity: 1,
        colors: ['#3C50E0'],
    },
    tooltip: {
        style: {
            fontSize: '12px',
        },
        y: {
            formatter: function (val) {
                return '৳' + val + 'k';
            },
        },
    },
};

const chartOne = new ApexCharts(document.querySelector('#chartOne'), chartOneOptions);
chartOne.render();

// Statistics Chart
const chartTwoOptions = {
    series: [{
        name: 'Product One',
        data: [168, 385, 201, 298, 187, 195, 291],
    }, {
        name: 'Product Two',
        data: [120, 115, 160, 145, 165, 132, 140],
    }],
    chart: {
        type: 'area',
        height: 310,
        toolbar: {
            show: false,
        },
    },
    colors: ['#5750f1', '#0FADCF'],
    dataLabels: {
        enabled: false,
    },
    stroke: {
        curve: 'smooth',
        width: 2,
    },
    grid: {
        strokeDashArray: 5,
        xaxis: {
            lines: {
                show: false,
            },
        },
        yaxis: {
            lines: {
                show: true,
            },
        },
    },
    fill: {
        type: 'gradient',
        gradient: {
            shade: 'dark',
            type: 'vertical',
            shadeIntensity: 0,
            gradientToColors: undefined,
            inverseColors: false,
            opacityFrom: 0.4,
            opacityTo: 0,
            stops: [0, 50, 100],
            colorStops: [],
        },
    },
    xaxis: {
        categories: ['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
        axisBorder: {
            show: false,
        },
        axisTicks: {
            show: false,
        },
    },
    legend: {
        show: false,
    },
    tooltip: {
        x: {
            show: false,
        },
    },
};

const chartTwo = new ApexCharts(document.querySelector('#chartTwo'), chartTwoOptions);
chartTwo.render();

// Monthly Target Progress Chart
const chartThreeOptions = {
    series: [75.55],
    chart: {
        type: 'radialBar',
        height: 335,
    },
    plotOptions: {
        radialBar: {
            hollow: {
                size: '60%',
            },
            dataLabels: {
                name: {
                    fontSize: '18px',
                    color: '#64748B',
                },
                value: {
                    fontSize: '24px',
                    fontWeight: '600',
                    color: '#1C2434',
                },
                total: {
                    show: true,
                    label: 'Target',
                    formatter: function (w) {
                        return '75.55%';
                    },
                },
            },
        },
    },
    colors: ['#3C50E0'],
    labels: ['Target'],
    legend: {
        show: false,
    },
};

const chartThree = new ApexCharts(document.querySelector('#chartThree'), chartThreeOptions);
chartThree.render();

// Monthly Sales Chart (Chart.js fallback)
const monthlySalesCtx = document.getElementById('monthlySalesChart')?.getContext('2d');
if (monthlySalesCtx) {
    const monthlySalesChart = new Chart(monthlySalesCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Sales',
                data: [65, 59, 80, 81, 56, 55, 40, 65, 59, 80, 81, 56],
                backgroundColor: '#3b82f6',
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        callback: function(value) {
                            return '৳' + value + 'K';
                        }
                    }
                }
            }
        }
    });
}

// Monthly Target Progress Chart (Doughnut)
const targetProgressCtx = document.getElementById('targetProgressChart')?.getContext('2d');
if (targetProgressCtx) {
    const targetProgressChart = new Chart(targetProgressCtx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [75.55, 24.45],
                backgroundColor: ['#3b82f6', '#e5e7eb'],
                borderWidth: 0,
                cutout: '80%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// Statistics Chart (Area Chart)
const statisticsCtx = document.getElementById('statisticsChart')?.getContext('2d');
if (statisticsCtx) {
    const statisticsChart = new Chart(statisticsCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [30, 40, 35, 50, 49, 60],
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    }
                }
            }
        }
    });
}

@if($role === 'Admin')
// Expenses & Bills Chart (keeping existing functionality)
if (document.getElementById('expensesChart')) {
    const expensesCtx = document.getElementById('expensesChart').getContext('2d');
    const expensesChart = new Chart(expensesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['months'] ?? []) !!},
            datasets: [{
                label: 'Monthly Expenses',
                data: {!! json_encode($chartData['expenses'] ?? []) !!},
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: false
            }, {
                label: 'Monthly Bills',
                data: {!! json_encode($chartData['bills'] ?? []) !!},
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '৳' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

// Orders Chart (keeping existing functionality)
if (document.getElementById('ordersChart')) {
    const ordersCtx = document.getElementById('ordersChart').getContext('2d');
    const ordersChart = new Chart(ordersCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Approved', 'Completed', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $stats['total_orders']['pending'] ?? 0 }},
                    {{ $stats['total_orders']['approved'] ?? 0 }},
                    {{ $stats['total_orders']['completed'] ?? 0 }},
                    {{ ($stats['total_orders']['total'] ?? 0) - ($stats['total_orders']['pending'] ?? 0) - ($stats['total_orders']['approved'] ?? 0) - ($stats['total_orders']['completed'] ?? 0) }}
                ],
                backgroundColor: [
                    '#007bff',
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}
@endif
</script>
@endpush
