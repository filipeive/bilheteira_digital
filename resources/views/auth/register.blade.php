<x-guest-layout>
    <x-slot name="heading">Criar Conta</x-slot>
    <x-slot name="subheading">Registe a sua equipa para operar o sistema</x-slot>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" value="Nome" />
            <div class="relative mt-1">
                <i data-lucide="user" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#D4AF37]"></i>
                <x-text-input id="name" class="block w-full pl-10" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nome completo" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <div class="relative mt-1">
                <i data-lucide="mail" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#D4AF37]"></i>
                <x-text-input id="email" class="block w-full pl-10" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="email@exemplo.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Senha" />
            <div class="relative mt-1">
                <i data-lucide="lock" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#D4AF37]"></i>
                <x-text-input id="password" class="block w-full pl-10"
                                type="password"
                                name="password"
                                required autocomplete="new-password"
                                placeholder="Crie uma senha" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar Senha" />
            <div class="relative mt-1">
                <i data-lucide="shield-check" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#D4AF37]"></i>
                <x-text-input id="password_confirmation" class="block w-full pl-10"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="Repita a senha" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="mt-6 w-full inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-[#D4AF37] to-[#B8960C] px-5 py-3 font-bold text-[#0D0B07] shadow-lg shadow-black/25 transition hover:-translate-y-0.5">
            <i data-lucide="user-plus" class="w-5 h-5"></i>
            Registar
        </button>

        <div class="mt-5 text-center text-sm">
            <a class="rounded-md text-[#F5E6A3] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#D4AF37]" href="{{ route('login') }}">
                Já tem conta? Entrar
            </a>
        </div>
    </form>
</x-guest-layout>
