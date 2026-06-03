@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => '
            w-full py-3 pr-4 rounded-lg
            bg-[#0D0B07] border border-[rgba(212,175,55,0.22)]
            text-[#F7F1E4] placeholder-[#5A5040]
            font-[Montserrat,sans-serif] text-sm
            transition duration-200
            focus:outline-none focus:border-[#D4AF37] focus:ring-2 focus:ring-[rgba(212,175,55,0.18)]
            disabled:opacity-50 disabled:cursor-not-allowed
        ' . ($attributes->has('class') && str_contains($attributes->get('class'), 'pl-') ? '' : ' pl-4')
    ]) }}
>
