<x-layouts.app :title="__('Post New Job')">
    <div class="min-h-screen bg-gradient-to-br from-blue-100 via-blue-50 to-blue-200">
        <!-- Simple Navbar -->
        <div class="bg-white border-b border-blue-200">
            <div class="max-w-7xl mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <div class="text-xl font-bold text-blue-900">Part Timer</div>
                    <div class="flex gap-4 items-center">
                        <a href="{{ route('recruiter.dashboard') }}"
                            class="text-blue-900 hover:text-blue-700 text-sm">Home</a>
                        <a href="{{ route('jobs.posted') }}" class="text-blue-900 hover:text-blue-700 text-sm">Posted
                            Jobs</a>
                        <a href="#" class="text-blue-900 hover:text-blue-700 text-sm">Inbox</a>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="text-blue-900 hover:text-blue-700 text-sm flex items-center gap-1">
                                Profile
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-blue-200 z-10">
                                <a href="#" class="block px-4 py-2 text-sm text-blue-900 hover:bg-blue-50">View Full
                                    Profile</a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-3xl mx-auto px-4 py-8">
            <!-- Page Header -->
            <div class="mb-6">
                <a href="{{ route('jobs.posted') }}"
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium mb-2 inline-block">
                    ← Back to Posted Jobs
                </a>
                <h2 class="text-2xl font-bold text-blue-900">Post a New Job</h2>
                <p class="text-blue-600 text-sm mt-1">Fill out the details below to post your job listing</p>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                    <strong class="block font-medium">There were some problems with your submission:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Job Form -->
            <div class="bg-white rounded-lg p-6">
                <form action="{{ route('jobs.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Job Title -->
                    <div class="space-y-2">
                        <label for="title" class="block text-sm font-medium text-blue-900">
                            Job Title
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                            class="w-full rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-blue-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            placeholder="Enter Job Name Here">
                        @error('title')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Job Description -->
                    <div class="space-y-2">
                        <label for="description" class="block text-sm font-medium text-blue-900">
                            Job Description
                        </label>
                        <textarea id="description" name="description" rows="4" required
                            class="w-full rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-blue-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            placeholder="Describe the job responsibilities, requirements, and expectations...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Difficulty Level -->
                    <div class="space-y-2">
                        <label for="difficulty" class="block text-sm font-medium text-blue-900">
                            Difficulty Level
                        </label>
                        <select id="difficulty" name="difficulty" required
                            class="w-full rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-blue-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">Select difficulty level</option>
                            <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                        @error('difficulty')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Working Hours -->
                    <div class="space-y-2">
                        <label for="working_hours" class="block text-sm font-medium text-blue-900">
                            Working Hours per Day
                        </label>
                        <input type="number" id="working_hours" name="working_hours" value="{{ old('working_hours') }}"
                            min="1" max="40" required
                            class="w-full rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-blue-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                            placeholder="Enter working hours per day" style="appearance: textfield;">
                        @error('working_hours')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment -->
                    <div class="space-y-2">
                        <label for="payment" class="block text-sm font-medium text-blue-900">
                            Payment (BDT)
                        </label>
                        <div class="flex items-center">
                            <span class="mr-2 text-blue-600">৳</span>
                            <input type="number" id="payment" name="payment" value="{{ old('payment') }}" min="0"
                                required
                                class="w-full rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-blue-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                placeholder="Enter payment amount" style="appearance: textfield;">
                        </div>
                        @error('payment')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-6">
                        <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg">
                            Post Job
                        </button>
                        <a href="{{ route('jobs.posted') }}"
                            class="flex-1 bg-white hover:bg-blue-50 text-blue-600 font-medium py-3 rounded-lg border border-blue-300 text-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>