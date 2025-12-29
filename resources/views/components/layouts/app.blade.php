<x-layouts.app.header :title="$title ?? null">
    <main class="flex-1">
        {{ $slot }}
    </main>
</x-layouts.app.header>
