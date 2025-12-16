<x-layouts.app :title="__('Job Seeker Dashboard')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold text-blue-900 mb-4">Welcome, {{ auth()->user()->name }}!</h2>
                    <p class="text-gray-600 mb-6">You're logged in as a Job Seeker. Here you can browse and apply for job opportunities.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Browse Jobs Card -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 shadow-md">
                            <h3 class="text-lg font-semibold text-blue-900 mb-2">Browse Jobs</h3>
                            <p class="text-blue-700 text-sm mb-4">Explore available job opportunities</p>
                            <a href="#" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                View Jobs
                            </a>
                        </div>

                        <!-- My Applications Card -->
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-6 shadow-md">
                            <h3 class="text-lg font-semibold text-green-900 mb-2">My Applications</h3>
                            <p class="text-green-700 text-sm mb-4">Track your job applications</p>
                            <a href="#" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                View Applications
                            </a>
                        </div>

                        <!-- My Profile Card -->
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 shadow-md">
                            <h3 class="text-lg font-semibold text-purple-900 mb-2">My Profile</h3>
                            <p class="text-purple-700 text-sm mb-4">Manage your profile information</p>
                            <a href="{{ route('profile.edit') }}" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
