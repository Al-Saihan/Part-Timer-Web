<x-layouts.auth>
    <div class="space-y-8">
        <!-- Header -->
        <div class="space-y-3 text-center">
            <h1 class="text-3xl font-bold text-blue-900">Sign-In</h1>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center rounded-lg bg-blue-50 p-4 text-blue-800 border border-blue-200"
            :status="session('status')" />

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
            @csrf

            <!-- Email Input -->
            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-blue-900">
                    Email Address
                </label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    autocomplete="email" placeholder="you@example.com"
                    class="w-full rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-blue-900 shadow-sm outline-none transition-all duration-200 focus:border-blue-500 focus:ring-3 focus:ring-blue-200 focus:shadow-blue-100" />
                @error('email')
                    <p class="text-sm text-red-600 mt-1 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Password Input -->
            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-blue-900">
                    Password
                </label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                    placeholder="Enter your password"
                    class="w-full rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-blue-900 shadow-sm outline-none transition-all duration-200 focus:border-blue-500 focus:ring-3 focus:ring-blue-200 focus:shadow-blue-100" />
                @error('password')
                    <p class="text-sm text-red-600 mt-1 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-xs font-medium text-blue-600 transition hover:text-blue-700 mt-2 inline-block">
                        Forgot password?
                    </a>
                @endif
            </div>



            <!-- Sign In Button -->
            <button type="submit"
                class="w-full rounded-lg bg-blue-600 px-4 py-3.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:bg-blue-700 hover:shadow-xl hover:scale-[1.02] focus:outline-none focus:ring-3 focus:ring-blue-300 focus:ring-offset-2 active:scale-[0.98]"
                data-test="login-button">
                Sign In
            </button>
        </form>

        <!-- Sign Up Link -->
        <div class="text-center pt-4 border-t border-blue-100">
            <p class="text-sm text-blue-800">
                Don't have an account?
                <a href="{{ route('register') }}"
                    class="font-semibold text-blue-600 transition hover:text-blue-700 ml-1">
                    Sign up now
                </a>
            </p>
        </div>
    </div>
</x-layouts.auth>