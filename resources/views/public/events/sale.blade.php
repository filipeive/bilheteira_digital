@extends('layouts.public')

@section('title', 'Comprar Bilhete — ' . $event->name)
@section('meta_description', 'Compre bilhetes online para ' . $event->name . ' — ' . ($event->venue ?? '') . ', ' . ($event->province ?? ''))

@section('content')

<div class="container" style="padding-top: 100px; padding-bottom: 60px;">

  {{-- Header compacto do evento --}}
  <div class="sale-event-header glass-card" style="margin-bottom: 32px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; border-left: 4px solid var(--gold);">
    <div style="display: flex; align-items: center; gap: 16px;">
      @if($event->logo_url)
        <img src="{{ $event->logo_url }}" alt="{{ $event->name }}" style="max-height: 50px; max-width: 120px; object-fit: contain;">
      @endif
      <div>
        <h1 class="sale-event-name" style="font-size: 1.8rem; color: var(--gold); margin: 0;">{{ $event->name }}</h1>
        <p class="sale-event-meta" style="color: var(--text-secondary); font-size: 0.85rem; margin: 4px 0 0 0;">
          <i data-lucide="calendar" class="w-3.5 h-3.5" style="display: inline; vertical-align: middle;"></i>
          {{ $event->starts_at?->format('d M Y') ?? $event->date?->format('d M Y') }}
          &nbsp;·&nbsp;
          <i data-lucide="map-pin" class="w-3.5 h-3.5" style="display: inline; vertical-align: middle;"></i>
          {{ $event->venue }}, {{ $event->province ?? $event->city }}
        </p>
      </div>
    </div>
    <a href="{{ route('events.show', $event->slug ?? $event->id) }}" class="btn-outline sale-back-link">
      <i data-lucide="arrow-left" class="w-4 h-4"></i> Voltar ao Evento
    </a>
  </div>

  {{-- Urgency Bar --}}
  @php
    $totalAvailable = 0;
    $ticketPrices = $event->getTicketTypePrices();
    foreach ($ticketPrices as $tp) {
        $totalAvailable += ($tp['lot_size'] ?? 0);
    }
  @endphp
  @if($totalAvailable > 0 && $totalAvailable <= 50)
  <div style="background: rgba(224,138,58,0.1); border: 1px solid rgba(224,138,58,0.3); border-radius: 8px; padding: 12px 16px; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: #E08A3A; margin-bottom: 24px;">
    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
    <span>Bilhetes quase esgotados — <strong>garanta já o seu!</strong></span>
  </div>
  @endif

  {{-- Escolha o tipo de bilhete --}}
  <div style="text-align: center; margin-bottom: 32px;">
    <span style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 18px; border-radius: 20px; border: 1px solid rgba(212,160,23,0.3); background: rgba(212,160,23,0.08); color: var(--gold); font-size: 0.8rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 12px;">
      <i data-lucide="ticket" class="w-4 h-4"></i> Seleccione o seu bilhete
    </span>
    <h2 style="font-size: 2.4rem; color: var(--text-primary); margin-bottom: 8px;">ESCOLHA O SEU BILHETE</h2>
    <p style="color: var(--text-secondary); font-size: 1rem;">
      Clique no tipo de bilhete pretendido para abrir o formulário de compra
    </p>
  </div>

  {{-- Ticket Type Cards Grid --}}
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px;">
    @foreach ($event->getPublicTicketTypePrices() as $key => $type)
      <div x-data @click="$dispatch('open-ticket-modal', { type: '{{ $key }}' })" class="glass-card" style="cursor: pointer; text-align: center; position: relative; overflow: hidden; transition: all 0.3s ease; border: 1px solid var(--dark-border);" onmouseover="this.style.transform='translateY(-5px)'; this.style.borderColor='{{ $type['color'] ?? 'var(--gold)' }}'" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='var(--dark-border)'">
        {{-- Glow bar --}}
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: {{ $type['color'] ?? 'var(--gold)' }};"></div>

        <div style="width: 58px; height: 58px; border-radius: 16px; margin: 0 auto 14px; display: grid; place-items: center; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); color: {{ $type['color'] ?? 'var(--gold)' }};">
          <i data-lucide="{{ str_starts_with($key, 'vip') ? 'star' : ($key === 'gate' ? 'door-open' : 'ticket') }}" class="w-8 h-8"></i>
        </div>

        <h3 style="font-size: 1.6rem; color: var(--text-primary); margin-bottom: 4px;">{{ $type['name'] ?? '' }}</h3>
        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 16px;">{{ $type['description'] ?? '' }}</p>

        <div style="margin-bottom: 12px;">
          <span class="mono" style="font-size: 2.2rem; font-weight: 700; color: {{ $type['color'] ?? 'var(--gold)' }};">
            {{ number_format($type['price'] ?? 0, 0, ',', '.') }}
          </span>
          <span style="color: var(--text-muted); font-size: 0.9rem;"> MT</span>
        </div>

        @if (isset($type['lot_size']) && $type['lot_size'] > 0)
          <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--dark-border); font-size: 0.75rem; padding: 4px 10px; border-radius: 6px;">
            Lote: {{ $type['lot_size'] }} bilhetes
          </span>
        @endif

        <div style="margin-top: 14px;">
          <span style="display: inline-flex; align-items: center; gap: 6px; color: {{ $type['color'] ?? 'var(--gold)' }}; font-size: 0.85rem; font-weight: 600;">
            <i data-lucide="arrow-right" class="w-4 h-4"></i> Seleccionar
          </span>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Formulário Livewire — passa o evento --}}
  @livewire('ticket-form', ['event' => $event])

</div>

{{-- Pagamentos aceites --}}
<div style="text-align: center; padding: 20px; margin-bottom: 40px;">
  <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 8px;">PAGAMENTOS ACEITES</p>
  <div style="display: inline-flex; gap: 24px; align-items: center;">
    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
      <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255,255,255,0.05); display: grid; place-items: center;">
      <img src="{{ asset('alpha-logo-gold.png') }}" alt="M-Pesa" style="width: 48px; height: 48px;">  
      <span style="font-weight: 800; font-size: 0.9rem; color: #E03A3A;">M-Pesa</span>
      </div>
    </div>
    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
      <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255,255,255,0.05); display: grid; place-items: center;">
        <img src="{{ asset('alpha-logo-gold.png') }}" alt="e-Mola" style="width: 48px; height: 48px;">
        <span style="font-weight: 800; font-size: 0.9rem; color: #F59E0B;">e-Mola</span>
      </div>
    </div>
  </div>
</div>

@endsection
