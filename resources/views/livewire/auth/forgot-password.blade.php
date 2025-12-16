<x-layouts.auth>
    <div class="flex flex-col gap-8">
        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-blue-900">Forgot Password?</h1>
            <p class="mt-2 text-sm text-blue-700">Enter your email and we'll send you a link to reset your password</p>
        </div>

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-blue-900 mb-2">Email Address</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    autofocus
                    placeholder="you@example.com"
                    class="w-full rounded-lg border border-blue-200 bg-white px-4 py-2.5 text-sm text-blue-900 placeholder-blue-400 transition focus:border-blue-500 focus:ring-2 focus:ring-blue-300 outline-none"
                />
                @error('email')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition mt-2"
            >
                Send Reset Link
            </button>
        </form>

        <div class="text-center text-sm text-blue-700">
            <span>Remember your password?</span>
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Sign In</a>
        </div>
    </div>
</x-layouts.auth>
