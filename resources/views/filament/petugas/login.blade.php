<x-filament-panels::page.simple>
    <x-slot name="heading">
        {{-- Role Badge --}}
        <div style="display:flex; justify-content:center; margin-bottom:16px;">
            <div class="role-badge petugas-badge">
                <span class="pulse-dot"></span>
                PETUGAS OPERASIONAL
            </div>
        </div>

        {{-- Heading --}}
        <h1 class="fi-simple-heading" style="font-size:26px; font-weight:800; color:#111827; text-align:center; margin:0 0 4px; letter-spacing:-0.5px; line-height:1.2;">
            {{ $this->getHeading() }}
        </h1>
    </x-slot>

    <x-slot name="subheading">
        <p class="fi-simple-subheading" style="font-size:14px; color:#6B7280; text-align:center; line-height:1.6; margin:0;">
            {{ $this->getSubHeading() }}
        </p>
    </x-slot>

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{-- Footer --}}
    <div style="margin-top:24px; text-align:center; font-size:12px; color:#9CA3AF; border-top:1px solid #D1FAE5; padding-top:18px; letter-spacing:0.02em;">
        Setor.in Sistem Petugas &middot; Akses terbatas sesuai bank sampah terdaftar
    </div>
</x-filament-panels::page.simple>
