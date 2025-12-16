<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <nav class="space-y-1">
            <a href="{{ route('profile.edit') }}" class="block rounded-md px-3 py-2 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800 {{ request()->routeIs('profile.edit') ? 'bg-zinc-100 dark:bg-zinc-800' : '' }}">{{ __('Profile') }}</a>
            <a href="{{ route('user-password.edit') }}" class="block rounded-md px-3 py-2 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800 {{ request()->routeIs('user-password.edit') ? 'bg-zinc-100 dark:bg-zinc-800' : '' }}">{{ __('Password') }}</a>
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <a href="{{ route('two-factor.show') }}" class="block rounded-md px-3 py-2 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800 {{ request()->routeIs('two-factor.show') ? 'bg-zinc-100 dark:bg-zinc-800' : '' }}">{{ __('Two-Factor Auth') }}</a>
            @endif
            <a href="{{ route('appearance.edit') }}" class="block rounded-md px-3 py-2 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800 {{ request()->routeIs('appearance.edit') ? 'bg-zinc-100 dark:bg-zinc-800' : '' }}">{{ __('Appearance') }}</a>
        </nav>
    </div>

    <div class="flex h-px w-full bg-zinc-200 dark:bg-zinc-800 md:hidden" aria-hidden="true"></div>

    <div class="flex-1 self-stretch max-md:pt-6">
        <h2 class="text-xl font-semibold text-zinc-900 dark:text-zinc-50">{{ $heading ?? '' }}</h2>
        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
