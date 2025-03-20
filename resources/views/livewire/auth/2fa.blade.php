<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $otp = '', $invalid = '';

    /**
     * Handle an incoming registration request.
     */
    public function verifyotp()
    {
        $validated = $this->validate([
            'otp' => 'required',

        ]);
        $valid = Google2FA::verifyKey(auth()->user()->google2fa_secret, $this->otp);
        if ($valid) {
            session(['google2fa' => true]);
            return redirect()->route("dashboard")->with("success", "2fa Verified");
        } else {
            $this->invalid = "Otp is not Valid";
        }
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Verify Otp')" :description="__('Enter your OTP for 2FA')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="verifyotp" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="otp"
            :label="__('OTP')"
            type="text"
            :placeholder="__('Enter OTP')" />
        @if($invalid)
        <div role="alert" aria-live="polite" aria-atomic="true" class="mt-3 text-sm font-medium text-red-500 dark:text-red-400" data-flux-error="">
            <svg class="shrink-0 [:where(&amp;)]:size-5 inline" data-flux-icon="" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"></path>
            </svg>
            {{ $invalid}}
        </div>
        @endif


        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary">
                {{ __('Verify') }}
            </flux:button>
        </div>
    </form>


</div>