@vite(['resources/css/app.css', 'resources/js/app.js'])

<x-layouts.app.header :title="$title ?? null">
    <main class="flex-1">
        {{ $slot }}
    </main>
</x-layouts.app.header>
