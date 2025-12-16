<x-layouts.auth>
    <div class="space-y-6">
        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-blue-900">Create Account</h1>
            <p class="mt-2 text-sm text-blue-700">Join Part Timer today</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-blue-900 mb-1">Full Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                    placeholder="John Doe"
                    class="w-full rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-blue-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                @error('name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-blue-900 mb-1">Email Address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    placeholder="you@example.com"
                    class="w-full rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-blue-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                @error('email')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-blue-900 mb-1">Password</label>
                <input id="password" name="password" type="password" required placeholder="Create a password"
                    class="w-full rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-blue-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                @error('password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-blue-900 mb-1">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="Confirm your password"
                    class="w-full rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-blue-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />
                @error('password_confirmation')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- User Type -->
            <div>
                <label class="block text-sm font-medium text-blue-900 mb-2">I want to:</label>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="user_type" value="seeker" {{ old('user_type') === 'seeker' ? 'checked' : '' }} required
                            class="h-4 w-4 border-blue-400 text-blue-600 focus:ring-2 focus:ring-blue-200" />
                        <span class="text-sm text-blue-900">I am looking for <b>Jobs</b>!</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="user_type" value="recruiter" {{ old('user_type') === 'recruiter' ? 'checked' : '' }} required
                            class="h-4 w-4 border-blue-400 text-blue-600 focus:ring-2 focus:ring-blue-200" />
                        <span class="text-sm text-blue-900">I am looking to <b>Hire</b>!</span>
                    </label>
                </div>
                @error('user_type')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Button -->
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition mt-2">
                Create Account
            </button>
        </form>

        <!-- Login Link -->
        <div class="text-center text-sm text-blue-700 pt-4">
            <span>Already have an account? </span>
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Sign In</a>
        </div>
    </div>
</x-layouts.auth>