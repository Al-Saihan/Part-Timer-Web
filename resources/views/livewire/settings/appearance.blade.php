<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <div class="flex flex-wrap gap-3" id="theme-toggle-group">
            <button type="button" data-set-theme="light" class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-800 transition hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-accent dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-800">
                {{ __('Light') }}
            </button>
            <button type="button" data-set-theme="dark" class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-800 transition hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-accent dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-800">
                {{ __('Dark') }}
            </button>
            <button type="button" data-set-theme="system" class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-800 transition hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-accent dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-800">
                {{ __('System') }}
            </button>
        </div>

        <script>
            (() => {
                const root = document.documentElement;
                const buttons = document.querySelectorAll('#theme-toggle-group [data-set-theme]');

                const applyTheme = (value) => {
                    const theme = value || 'system';
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (theme === 'dark' || (theme === 'system' && prefersDark)) {
                        root.classList.add('dark');
                    } else {
                        root.classList.remove('dark');
                    }
                    localStorage.setItem('theme', theme);
                    buttons.forEach((btn) => {
                        const active = btn.dataset.setTheme === theme;
                        btn.classList.toggle('bg-zinc-900', active);
                        btn.classList.toggle('text-white', active);
                        btn.classList.toggle('border-zinc-800', active);
                    });
                };

                buttons.forEach((btn) => {
                    btn.addEventListener('click', () => applyTheme(btn.dataset.setTheme));
                });

                applyTheme(localStorage.getItem('theme'));
            })();
        </script>
    </x-settings.layout>
</section>
