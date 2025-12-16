<?php

use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Symfony\Component\HttpFoundation\Response;

new class extends Component {
    #[Locked]
    public bool $twoFactorEnabled;

    #[Locked]
    public bool $requiresConfirmation;

    #[Locked]
    public string $qrCodeSvg = '';

    #[Locked]
    public string $manualSetupKey = '';

    public bool $showModal = false;

    public bool $showVerificationStep = false;

    #[Validate('required|string|size:6', onUpdate: false)]
    public string $code = '';

    /**
     * Mount the component.
     */
    public function mount(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        abort_unless(Features::enabled(Features::twoFactorAuthentication()), Response::HTTP_FORBIDDEN);

        if (Fortify::confirmsTwoFactorAuthentication() && is_null(auth()->user()->two_factor_confirmed_at)) {
            $disableTwoFactorAuthentication(auth()->user());
        }

        $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        $this->requiresConfirmation = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
    }

    /**
     * Enable two-factor authentication for the user.
     */
    public function enable(EnableTwoFactorAuthentication $enableTwoFactorAuthentication): void
    {
        $enableTwoFactorAuthentication(auth()->user());

        if (! $this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }

        $this->loadSetupData();

        $this->showModal = true;
    }

    /**
     * Load the two-factor authentication setup data for the user.
     */
    private function loadSetupData(): void
    {
        $user = auth()->user();

        try {
            $this->qrCodeSvg = $user?->twoFactorQrCodeSvg();
            $this->manualSetupKey = decrypt($user->two_factor_secret);
        } catch (Exception) {
            $this->addError('setupData', 'Failed to fetch setup data.');

            $this->reset('qrCodeSvg', 'manualSetupKey');
        }
    }

    /**
     * Show the two-factor verification step if necessary.
     */
    public function showVerificationIfNecessary(): void
    {
        if ($this->requiresConfirmation) {
            $this->showVerificationStep = true;

            $this->resetErrorBag();

            return;
        }

        $this->closeModal();
    }

    /**
     * Confirm two-factor authentication for the user.
     */
    public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirmTwoFactorAuthentication): void
    {
        $this->validate();

        $confirmTwoFactorAuthentication(auth()->user(), $this->code);

        $this->closeModal();

        $this->twoFactorEnabled = true;
    }

    /**
     * Reset two-factor verification state.
     */
    public function resetVerification(): void
    {
        $this->reset('code', 'showVerificationStep');

        $this->resetErrorBag();
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function disable(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        $disableTwoFactorAuthentication(auth()->user());

        $this->twoFactorEnabled = false;
    }

    /**
     * Close the two-factor authentication modal.
     */
    public function closeModal(): void
    {
        $this->reset(
            'code',
            'manualSetupKey',
            'qrCodeSvg',
            'showModal',
            'showVerificationStep',
        );

        $this->resetErrorBag();

        if (! $this->requiresConfirmation) {
            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
        }
    }

    /**
     * Get the current modal configuration state.
     */
    public function getModalConfigProperty(): array
    {
        if ($this->twoFactorEnabled) {
            return [
                'title' => __('Two-Factor Authentication Enabled'),
                'description' => __('Two-factor authentication is now enabled. Scan the QR code or enter the setup key in your authenticator app.'),
                'buttonText' => __('Close'),
            ];
        }

        if ($this->showVerificationStep) {
            return [
                'title' => __('Verify Authentication Code'),
                'description' => __('Enter the 6-digit code from your authenticator app.'),
                'buttonText' => __('Continue'),
            ];
        }

        return [
            'title' => __('Enable Two-Factor Authentication'),
            'description' => __('To finish enabling two-factor authentication, scan the QR code or enter the setup key in your authenticator app.'),
            'buttonText' => __('Continue'),
        ];
    }
} ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout
        :heading="__('Two Factor Authentication')"
        :subheading="__('Manage your two-factor authentication settings')"
    >
        <div class="flex flex-col w-full mx-auto space-y-6 text-sm" wire:cloak>
            @if ($twoFactorEnabled)
                <div class="space-y-4 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-900 dark:bg-green-950">
                    <div class="flex items-center gap-2">
                        <span class="inline-block rounded-full bg-green-600 px-2 py-1 text-xs font-medium text-white">{{ __('Enabled') }}</span>
                    </div>

                    <p class="text-sm text-zinc-700 dark:text-zinc-300">
                        {{ __('With two-factor authentication enabled, you will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
                    </p>

                    <livewire:settings.two-factor.recovery-codes :$requiresConfirmation/>

                    <div class="flex justify-start pt-2">
                        <button
                            type="button"
                            class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                            wire:click="disable"
                        >
                            {{ __('Disable 2FA') }}
                        </button>
                    </div>
                </div>
            @else
                <div class="space-y-4 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-900 dark:bg-red-950">
                    <div class="flex items-center gap-2">
                        <span class="inline-block rounded-full bg-red-600 px-2 py-1 text-xs font-medium text-white">{{ __('Disabled') }}</span>
                    </div>

                    <p class="text-sm text-zinc-700 dark:text-zinc-300">
                        {{ __('When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.') }}
                    </p>

                    <div class="flex justify-start pt-2">
                        <button
                            type="button"
                            class="rounded-md bg-accent px-4 py-2 text-sm font-semibold text-accent-foreground shadow-sm transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-accent"
                            wire:click="enable"
                        >
                            {{ __('Enable 2FA') }}
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </x-settings.layout>

    <div
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 px-4"
        wire:model="showModal"
        @if($showModal) @style('display: flex') @endif
    >
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-lg dark:bg-zinc-900">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-50">{{ $this->modalConfig['title'] }}</h3>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $this->modalConfig['description'] }}</p>

            @if ($showVerificationStep)
                <form wire:submit="confirmTwoFactor" class="mt-6 space-y-6">
                    <div class="space-y-2">
                        <label for="auth-code" class="text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ __('Authentication Code') }}</label>
                        <input
                            id="auth-code"
                            type="text"
                            inputmode="numeric"
                            maxlength="6"
                            wire:model="code"
                            class="w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-center text-lg tracking-widest text-zinc-900 shadow-sm outline-none ring-accent/40 focus:border-accent focus:ring-2 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                            placeholder="••••••"
                        />
                        @error('code')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <button
                            type="button"
                            class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-800 transition hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-accent dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-800"
                            wire:click="resetVerification"
                        >
                            {{ __('Back') }}
                        </button>
                        <button
                            type="submit"
                            class="rounded-md bg-accent px-4 py-2 text-sm font-semibold text-accent-foreground shadow-sm transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-accent"
                        >
                            {{ __('Confirm') }}
                        </button>
                    </div>
                </form>
            @else
                <div class="mt-6 space-y-4">
                    @if (!empty($qrCodeSvg))
                        <div class="flex justify-center">
                            <div class="w-48 rounded-lg border border-zinc-200 bg-white p-2 dark:border-zinc-700 dark:bg-zinc-800">
                                {!! $qrCodeSvg !!}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <p class="text-center text-xs text-zinc-600 dark:text-zinc-400">{{ __('or, enter the code manually') }}</p>
                            <div class="flex items-center gap-2 rounded-md border border-zinc-300 dark:border-zinc-700">
                                <input
                                    type="text"
                                    readonly
                                    value="{{ $manualSetupKey }}"
                                    class="flex-1 bg-transparent px-3 py-2 text-sm text-zinc-900 outline-none dark:text-zinc-100"
                                />
                                <button
                                    type="button"
                                    class="px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100"
                                    onclick="navigator.clipboard.writeText('{{ $manualSetupKey }}')"
                                >
                                    {{ __('Copy') }}
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-4">
                        <button
                            type="button"
                            class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-800 transition hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-accent dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-800"
                            wire:click="closeModal"
                        >
                            {{ __('Close') }}
                        </button>
                        <button
                            type="button"
                            class="rounded-md bg-accent px-4 py-2 text-sm font-semibold text-accent-foreground shadow-sm transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-accent"
                            wire:click="showVerificationIfNecessary"
                        >
                            {{ $this->modalConfig['buttonText'] }}
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
