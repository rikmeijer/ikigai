@props([
    'id' => 'virtual-select' .  str()->random(10),
    'options' => [],
    'value' => [],
    'action' => ''
])

@php
$virtualSelectOptions = collect([
    'ele' => '#'.$id,
    'options' => $options,
    'selectedValue' => $value,
    'placeholder' => 'Select an option',
    'multiple' => true,
    'allowNewOption' => true
]);
@endphp
 
 
<div wire:ignore wire:key="{{ md5($virtualSelectOptions) }}">
   
	<div
		x-data="{
			select: @entangle($attributes->wire('model')),
			initVirtualSelect() {
				VirtualSelect.init({{ $virtualSelectOptions }})
			}
		}"
		id="{{ $id }}"
		x-ref="virtualSelect"
		x-init="initVirtualSelect()"
		x-cloak
		x-on:change="select = event.target.value"
	></div>
</div>
 
@once
    @push('js')
    <script src="https://cdn.jsdelivr.net/npm/virtual-select-plugin@1.0.39/dist/virtual-select.min.js" integrity="sha256-Gsn2XyJGdUeHy0r4gaP1mJy1JkLiIWY6g6hJhV5UrIw=" crossorigin="anonymous"></script>
    @endpush

    @push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/virtual-select-plugin@1.0.39/dist/virtual-select.min.css" integrity="sha256-KqTuc/vUgQsb5EMyyxWf62qYinMUXDpWELyNx+cCUr0=" crossorigin="anonymous">
    @endpush	
@endonce