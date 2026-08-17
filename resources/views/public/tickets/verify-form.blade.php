@extends('layouts.public')

@section('title', 'Verificar Bilhete — Alpha Bilheteira')

@section('content')
<div class="container" style="padding-top: 120px; padding-bottom: 80px; max-width: 600px;">
  <div class="glass-card" style="text-align: center; padding: 40px 24px;">
    <div style="width: 64px; height: 64px; background: rgba(212,175,55,0.1); border: 1px solid var(--gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; color: var(--gold);">
      <i data-lucide="shield-check" class="w-8 h-8"></i>
    </div>

    <h1 style="font-size: 2.2rem; color: var(--gold); margin-bottom: 8px;">VERIFICAR BILHETE</h1>
    <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 28px;">
      Insira o código alfanumérico do bilhete para confirmar a sua autenticidade.
    </p>

    <form method="GET" action="{{ route('tickets.verify.form') }}" style="display: flex; flex-direction: column; gap: 16px;">
      <div>
        <input type="text" name="code" value="{{ request('code') }}"
               placeholder="Ex: REN-A1B2C3"
               required style="text-transform: uppercase;"
               class="form-input mono">
      </div>
      <button type="submit" class="btn-gold" style="width: 100%;">
        <i data-lucide="search" class="w-5 h-5"></i> Verificar Autenticidade
      </button>
    </form>
  </div>
</div>
@endsection
