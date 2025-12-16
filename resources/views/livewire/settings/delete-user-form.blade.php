<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">{{ __('Delete account') }}</h3>
        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('Delete your account and all of its resources') }}</p>
    </div>

    <form method="POST" wire:submit="deleteUser" class="space-y-4" onsubmit="return confirm('{{ __('Are you sure you want to delete your account? This action cannot be undone.') }}')">
        @csrf
        <div class="space-y-2">
            <label for="delete_password" class="text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ __('Password') }}</label>
            <input
                id="delete_password"
                type="password"
                wire:model="password"
                required
                class="w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm outline-none ring-accent/40 focus:border-accent focus:ring-2 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
            />
            @error('password')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-800 hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-accent dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-800" onclick="this.form.reset()">
                {{ __('Cancel') }}
            </button>
            <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500" data-test="confirm-delete-user-button">
                {{ __('Delete account') }}
            </button>
        </div>
    </form>
</section>
