@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-bold uppercase tracking-widest text-[#B8A890] mb-1']) }}>
    {{ $value ?? $slot }}
</label>
