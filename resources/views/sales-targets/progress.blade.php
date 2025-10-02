<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sales Target Progress') }} - {{ $target->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Target Overview -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Target Overview</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-sm text-blue-600">Target Amount</div>
                                <div class="text-2xl font-bold text-blue-800">${{ number_format($progressData['target_amount'], 2) }}</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-sm text-green-600">Current Achievement</div>
                                <div class="text-2xl font-bold text-green-800">${{ number_format($progressData['current_achievement'], 2) }}</div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="text-sm text-purple-600">Progress</div>
                                <div class="text-2xl font-bold text-purple-800">{{ $progressData['overall_progress'] }}%</div>
                            </div>
                            <div class="bg-orange-50 p-4 rounded-lg">
                                <div class="text-sm text-orange-600">Status</div>
                                <div class="text-lg font-semibold text-orange-800 capitalize">{{ $target->status }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Overall Progress</span>
                            <span class="text-sm font-medium text-gray-700">{{ $progressData['overall_progress'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progressData['overall_progress'] }}%"></div>
                        </div>
                    </div>

                    <!-- Monthly Progress Chart Placeholder -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Monthly Progress</h3>
                        <div class="bg-gray-50 p-4 rounded-lg text-center text-gray-500">
                            Chart will be displayed here showing monthly progress vs targets
                        </div>
                    </div>

                    <!-- Target Details -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Target Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Assigned To</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $target->assignedUser->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Target Year</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $target->target_year }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Created By</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $target->creator->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Created Date</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $target->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-2">
                        <a href="{{ route('sales-targets.show', $target) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            View Details
                        </a>
                        <a href="{{ route('sales-targets.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Back to List
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
