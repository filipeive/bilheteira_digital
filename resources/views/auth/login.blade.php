<x-guest-layout>
    <x-slot name="heading">Área de Membro</x-slot>
    <x-slot name="subheading">Entre para gerir vendas, validações e relatórios</x-slot>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <div class="relative mt-1">
                <i data-lucide="mail" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#D4AF37]"></i>
                <x-text-input id="email" class="block w-full pl-10" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="email@exemplo.com" />
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
                                required autocomplete="current-password"
                                placeholder="Digite a sua senha" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-[#D4AF37]/30 bg-[#0D0B07] text-[#D4AF37] shadow-sm focus:ring-[#D4AF37]" name="remember">
                <span class="ms-2 text-sm text-[#B8A890]">Lembrar sessão</span>
            </label>
        </div>

        <button type="submit" class="mt-6 w-full inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-[#D4AF37] to-[#B8960C] px-5 py-3 font-bold text-[#0D0B07] shadow-lg shadow-black/25 transition hover:-translate-y-0.5">
            <i data-lucide="log-in" class="w-5 h-5"></i>
            Entrar
        </button>

        <div class="mt-5 flex flex-wrap items-center justify-between gap-3 text-sm">
            @if (Route::has('password.request'))
                <a class="rounded-md text-[#F5E6A3] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#D4AF37]" href="{{ route('password.request') }}">
                    Esqueceu a senha?
                </a>
            @endif

            <a class="rounded-md text-[#F5E6A3] hover:text-white focus:outline-none focus:ring-2 focus:ring-[#D4AF37]" href="{{ route('register') }}">
                Criar conta
            </a>
        </div>
    </form>
</x-guest-layout>
