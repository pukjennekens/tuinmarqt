<?php
use Livewire\Volt\Component;
use App\Models\Setting;

new class extends Component
{
    /**
     * @var array $settings The settings as they are configured, also used for validation
     */
    public array $settings = [];

    /**
     * Provide any additional data for the component.
     *
     * @return array
     */
    public function with(): array
    {
        return [
            'globalSettings' => config('constants.global_settings'),
        ];
    }

    /**
     * Mount the component
     *
     * @return void
     */
    public function mount()
    {
        foreach (config('constants.global_settings') as $key => $settingData) {
            $this->settings[$key] = Setting::get($key);
        }
    }

    /**
     * Save the settings to the database
     *
     * @return void
     */
    public function save()
    {
        foreach ($this->settings as $key => $value) {
            Setting::set($key, $value);
        }

        $this->dispatch('notify', type: 'success', message: 'Instellingen opgeslagen');
    }
};
?>

<form wire:submit.prevent="save" class="space-y-4" autocomplete="off">
    @foreach ($globalSettings as $key => $settingData)
        <div>
            <x-input-label for="{{ $key }}" :value="$settingData['name']" />
            <x-text-input :id="$key" class="block mt-1 w-full" :type="$settingData['type']" name="email" required data-lpignore="true" data-form-type="other"  wire:model="settings.{{ $key }}" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
    @endforeach

    <x-primary-button type="submit">
        Opslaan

        <i class="fa-solid fa-circle-notch ml-2 animate-spin" wire:loading.delay.shorter wire:loading.attr="disabled" wire:target="save"></i>
    </x-primary-button>
</form>