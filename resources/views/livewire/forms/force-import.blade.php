<?php
use Livewire\Volt\Component;
use App\Models\Setting;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ImportArticles;
use App\Jobs\ImportArticleGroups;
use App\Jobs\ExportArticles;

new class extends Component
{
    /**
     * @var bool $disabled
     */
    public $disabled = false;

    /**
     * Force the import of the data
     *
     * @return void
     */
    public function forceImport()
    {
        if(Queue::connection('database')->size() > 0) {
            $this->dispatch('notify', type: 'error', message: 'Er is al een import bezig, wacht tot deze klaar is.');
            return;
        }

        // Call the import job
        dispatch(new ImportArticles());
        dispatch(new ImportArticleGroups());

        $this->disabled = true;
        $this->dispatch('notify', type: 'success', message: 'Import gestart');
        $this->dispatch('import-force-started');
    }

    /**
     * Force the export of the data
     *
     * @return void
     */
    public function forceExport()
    {
        if(Queue::connection('database')->size() > 0) {
            $this->dispatch('notify', type: 'error', message: 'Er is al een import bezig, wacht tot deze klaar is.');
            return;
        }

        // Call the export job
        dispatch(new ExportArticles());

        $this->disabled = true;
        $this->dispatch('notify', type: 'success', message: 'Export gestart');
        $this->dispatch('import-force-started');
    }

    /**
     * Mount the component
     *
     * @return void
     */
    public function mount()
    {
        $this->disabled = Queue::connection('database')->size() > 0;
    }
};
?>

<div>
    @if($disabled)
        <p>
            Er is al een import bezig, wacht tot deze klaar is.
        </p>
    @else
        <div class="inline-flex gap-2">
            <div
                x-data="{ confirm: false }"
                x-on:import-force-started.window="confirm = false;"
            >
                <x-primary-button 
                    type="button"
                    x-show="!confirm"
                    x-on:click="confirm = true"
                >
                    Import forceren
                </x-primary-button>

                <x-primary-button 
                    type="button" 
                    x-show="confirm" 
                    wire:click="forceImport"
                    x-on:click.away="confirm = false"
                >
                    Bevestigen

                    <i class="fa-solid fa-circle-notch ml-2 animate-spin" wire:loading.delay.shorter wire:loading.attr="disabled" wire:target="forceImport"></i>
                </x-primary-button>
            </div>

            <div
                x-data="{ confirm: false }"
                x-on:import-export-started.window="confirm = false;"
            >
                <x-primary-button 
                    type="button"
                    x-show="!confirm"
                    x-on:click="confirm = true"
                >
                    Export forceren
                </x-primary-button>

                <x-primary-button 
                    type="button" 
                    x-show="confirm" 
                    wire:click="forceExport"
                    x-on:click.away="confirm = false"
                >
                    Bevestigen

                    <i class="fa-solid fa-circle-notch ml-2 animate-spin" wire:loading.delay.shorter wire:loading.attr="disabled" wire:target="forceExport"></i>
                </x-primary-button>
            </div>
        </div>
    @endif
</div>