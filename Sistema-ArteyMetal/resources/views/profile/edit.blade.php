<x-app-layout>
    <x-slot name="header">
        <span>Configuracion</span>
    </x-slot>

    <div class="space-y-5">
        <div class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-[#2a2419]">Ajustes de cuenta</h2>
            <p class="mt-1 text-sm text-[#6e6758]">Administra datos personales, seguridad y cierre de cuenta.</p>
        </div>

        <div class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="rounded-2xl border border-[#e5dec8] bg-white p-5 shadow-sm">
            @include('profile.partials.update-password-form')
        </div>

        <div class="rounded-2xl border border-rose-200 bg-white p-5 shadow-sm">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
