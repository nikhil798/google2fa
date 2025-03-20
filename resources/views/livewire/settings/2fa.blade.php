<?php

use Livewire\Volt\Component;

new class extends Component {
    //
    public $user, $urlQRCode, $secret, $otp;

    public function mount()
    {
        $this->user = auth()->user();
        $google2fa = app('pragmarx.google2fa');
        if($this->user->google2fa_secret){
            $this->secret = $this->user->google2fa_secret; 
        }else{
            $this->secret = $google2fa->generateSecretKey() ;
        }
        $this->urlQRCode = $google2fa->getQRCodeInline(
            config("app.name"),
            $this->user->email,
            $this->secret,

        );
    }
    public function verifyotp(){
        $this->validate([
            "otp" => "required"
        ]);
        $valid = Google2FA::verifyKey($this->secret, $this->otp);
        if($valid){
            $this->user->google2fa_secret = $this->secret;
            $this->user->save();
            return redirect()->route("dashboard")->with("success","2fa Verified");
        }
    }
}; ?>

<div class="flex flex-col items-start">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Google 2fa')" :subheading=" __('Update the appearance settings for your account')">
        <div>
            <p>key = {{ $secret }}</p>
            {!! $urlQRCode !!}
        </div>
        <div class="pt-3">
            <form wire:submit="verifyotp">
                <flux:input wire:model="otp" :label="__('Enter Otp')" type="text"  />
                <div class="pt-3">
                <flux:button variant="primary" type="submit" >{{ __('Verify') }}</flux:button>
                </div>
            </form>
        </div>
    </x-settings.layout>
</div>