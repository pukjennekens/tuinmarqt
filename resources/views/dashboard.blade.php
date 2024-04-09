@php
    use Illuminate\Support\Facades\Queue;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Instellingen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-dashboard-tabs>
                <x-dashboard-tab title="Globale instellingen">
                    <livewire:forms.global-settings />
                </x-dashboard-tab>

                <x-dashboard-tab title="Import/export forceren" :status="Queue::size() == 0 ? 'success' : 'warning'">
                    <livewire:forms.force-import />
                </x-dashboard-tab>

                <x-dashboard-tab title="Instructies">
                    
                </x-dashboard-tab>
            </x-dashboard-tabs>
        </div>
    </div>
</x-app-layout>
