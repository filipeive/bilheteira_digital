@extends('layouts.public')

@section('title', 'Resultado da Verificação — Alpha Bilheteira')

@section('content')
<div class="container" style="padding-top: 120px; padding-bottom: 80px; max-width: 600px;">
  <div class="glass-card" style="text-align: center; padding: 40px 24px;">

    @if($ticket)
      @php
        $isValid = in_array($ticket->status, ['confirmed', 'used']);
      @endphp
      <div style="width: 72px; height: 72px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;
                  {{ $isValid ? 'background: rgba(16,185,129,0.15); color: #34D399; border: 2px solid #10B981;' : 'background: rgba(239,68,68,0.15); color: #F87171; border: 2px solid #EF4444;' }}">
        @if($ticket->status === 'confirmed')
          <i data-lucide="check-circle-2" class="w-10 h-10"></i>
        @elseif($ticket->status === 'used')
          <i data-lucide="scan-line" class="w-10 h-10"></i>
        @else
          <i data-lucide="x-circle" class="w-10 h-10"></i>
        @endif
      </div>

      <div style="font-family: 'Bebas Neue', cursive; font-size: 2.4rem; letter-spacing: 1px; margin-bottom: 16px;
                  {{ $isValid ? 'color: #34D399;' : 'color: #F87171;' }}">
        @switch($ticket->status)
          @case('confirmed') BILHETE VÁLIDO @break
          @case('used')      BILHETE JÁ UTILIZADO @break
          @case('pending')   PAGAMENTO PENDENTE @break
          @case('cancelled') BILHETE CANCELADO @break
        @endswitch
      </div>

      <div style="background: rgba(0,0,0,0.3); border: 1px solid var(--dark-border); border-radius: 12px; padding: 20px; text-align: left; margin-bottom: 28px;">
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px; margin-bottom: 10px;">
          <span style="color: var(--text-muted); font-size: 0.85rem;">Titular:</span>
          <strong style="color: var(--text-primary); font-size: 0.95rem;">{{ $ticket->buyer_name }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px; margin-bottom: 10px;">
          <span style="color: var(--text-muted); font-size: 0.85rem;">Evento:</span>
          <strong style="color: var(--gold); font-size: 0.95rem;">{{ $ticket->event->name ?? 'Evento' }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px; margin-bottom: 10px;">
          <span style="color: var(--text-muted); font-size: 0.85rem;">Tipo:</span>
          <span class="badge badge-gold">{{ $ticket->getTicketTypeLabel() }}</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: var(--text-muted); font-size: 0.85rem;">Código:</span>
          <span class="mono" style="color: var(--gold-light); font-weight: 700;">{{ $ticket->ticket_code }}</span>
        </div>
      </div>

    @else
      <div style="width: 72px; height: 72px; background: rgba(239,68,68,0.15); color: #F87171; border: 2px solid #EF4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
        <i data-lucide="x-circle" class="w-10 h-10"></i>
      </div>
      <h1 style="font-size: 2.2rem; color: #F87171; margin-bottom: 8px;">BILHETE NÃO ENCONTRADO</h1>
      <p style="color: var(--text-secondary); margin-bottom: 28px;">
        O código <strong class="mono" style="color: var(--gold);">"{{ $code }}"</strong> não existe no sistema.
      </p>
    @endif

    {{-- Pesquisar outro --}}
    <form method="GET" action="{{ route('tickets.verify.form') }}" style="display: flex; gap: 10px;">
      <input type="text" name="code" placeholder="Inserir outro código (ex: REN-A1B2C3)"
             required style="text-transform: uppercase;"
             class="form-input mono">
      <button type="submit" class="btn-gold" style="white-space: nowrap;">
        Verificar
      </button>
    </form>
  </div>
</div>
@endsection
