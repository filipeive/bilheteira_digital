@extends('layouts.public')

@section('title', 'Os Meus Bilhetes — Alpha Bilheteira')

@section('content')
<div class="container" style="padding-top: 120px; padding-bottom: 80px; max-width: 800px;">
  <div class="glass-card" style="padding: 40px 28px;">
    <div style="text-align: center; margin-bottom: 32px;">
      <div style="width: 64px; height: 64px; background: rgba(212,175,55,0.1); border: 1px solid var(--gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; color: var(--gold);">
        <i data-lucide="ticket" class="w-8 h-8"></i>
      </div>
      <h1 style="font-size: 2.2rem; color: var(--gold); margin-bottom: 8px;">OS MEUS BILHETES</h1>
      <p style="color: var(--text-secondary); font-size: 0.95rem;">
        Insira o seu número de telemóvel ou email para encontrar os seus bilhetes de eventos.
      </p>
    </div>

    <form method="POST" action="{{ route('tickets.search') }}" style="display: flex; gap: 12px; margin-bottom: 32px; flex-wrap: wrap;">
      @csrf
      <input type="text" name="phone_or_email"
             value="{{ $query ?? '' }}"
             placeholder="+258 84 000 0000 ou email@exemplo.com"
             required
             class="form-input" style="flex: 1; min-width: 240px;">
      <button type="submit" class="btn-gold" style="white-space: nowrap;">
        <i data-lucide="search" class="w-4 h-4"></i> Pesquisar Bilhetes
      </button>
    </form>

    @if(isset($tickets))
      @if($tickets->isEmpty())
        <div style="text-align: center; padding: 40px 20px; background: rgba(0,0,0,0.2); border-radius: 12px; border: 1px dashed var(--dark-border);">
          <i data-lucide="ticket-x" class="w-12 h-12" style="color: var(--text-muted); margin-bottom: 12px;"></i>
          <p style="color: var(--text-secondary); font-size: 1rem;">Nenhum bilhete encontrado para <strong>"{{ $query }}"</strong>.</p>
          <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">Verifique se digitou o número/email correctamente.</p>
        </div>
      @else
        <h3 style="font-size: 1.3rem; color: var(--gold); margin-bottom: 16px;">Bilhetes Encontrados ({{ $tickets->count() }})</h3>
        <div style="display: flex; flex-direction: column; gap: 16px;">
          @foreach($tickets as $ticket)
          <div style="background: rgba(0,0,0,0.4); border: 1px solid var(--dark-border); border-radius: 12px; padding: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div>
              <strong style="font-size: 1.1rem; color: var(--text-primary); display: block;">{{ $ticket->event->name ?? 'Evento' }}</strong>
              <span style="font-size: 0.85rem; color: var(--text-secondary);">
                <i data-lucide="calendar" class="w-3.5 h-3.5" style="display: inline; vertical-align: middle;"></i>
                {{ $ticket->event->starts_at?->format('d M Y') ?? $ticket->event->date?->format('d M Y') }}
                &nbsp;·&nbsp; {{ $ticket->buyer_name }}
              </span>
            </div>
            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
              <div style="text-align: right;">
                <span class="mono" style="font-size: 1.1rem; font-weight: 700; color: var(--gold-light); display: block;">{{ $ticket->ticket_code }}</span>
                <span class="badge badge-gold">{{ $ticket->getTicketTypeLabel() }}</span>
              </div>
              <span class="badge {{ $ticket->getStatusColor() == 'green' ? 'badge-green' : ($ticket->getStatusColor() == 'blue' ? 'badge-blue' : 'badge-yellow') }}">
                {{ $ticket->getStatusLabel() }}
              </span>
              <a href="{{ route('tickets.download', $ticket) }}" class="btn-outline btn-sm">
                <i data-lucide="download" class="w-4 h-4"></i> PDF
              </a>
            </div>
          </div>
          @endforeach
        </div>
      @endif
    @endif
  </div>
</div>
@endsection
