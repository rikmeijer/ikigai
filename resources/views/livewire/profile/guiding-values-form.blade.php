<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use function Livewire\Volt\rules;
use function Livewire\Volt\state;

state([
    'options' => \App\Models\Value::all()->map(fn($value) => $value->name),
    'guiding_values' => Auth::user()->guidingValues->map(fn($value) => $value->name)
]);

rules([
    'guiding_values' => [] 
]);

$updateGuidingValues = function (array $guidingValues) {
    $this->guiding_values = $guidingValues;
    try {
        $validated = $this->validate();
    } catch (ValidationException $e) {
        $this->reset('guiding_values');
        throw $e;
    }
    
    \App\Models\Value::upsert(array_map(fn($value) => ['name' => $value], $guidingValues), ['name']);
    Auth::user()->guidingValues()->sync($this->guiding_values);
    Auth::user()->save();
    
    //$this->reset('guiding_values');

    //$this->dispatch('guiding-values-updated');
};

?>
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Guiding values') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Which values are natural to you? Which can guide you when making choices?') }}
        </p>
    </header>

    <div>
        
        <x-selectbox 
            wire:model="updateGuidingValues"
            :options="$options"
            :value="$guiding_values"
        />
        
        <x-input-label for="update_guiding_values_values" :value="__('Guiding values')" />
        <div id="update_guiding_values_values" wire:ignore></div>
        @push('js')
            <script>
                VirtualSelect.init({
                    ele: '#update_guiding_values_values',
                    multiple: true,
                    options: @json($options),
                    selectedValue: @json($guiding_values),
                    allowNewOption: true
                });
                document.querySelector('#update_guiding_values_values').addEventListener('change', (e) => @this.updateGuidingValues(e.currentTarget.value));
            </script>
        @endpush
        <x-input-error :messages="$errors->get('guiding_values')" class="mt-2" />
    </div>
</section>
