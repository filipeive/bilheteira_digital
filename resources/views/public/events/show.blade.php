@extends('layouts.public')

@section('title', $meta['title'] ?? $event->name)
@section('meta_description', $meta['description'] ?? '')
@section('og_image', $meta['image'] ?? '')

@section('content')

@if (session('warning'))
<div class="container" style="padding-top: 90px; margin-bottom: -70px; position: relative; z-index: 10;">
  <div style="background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); color: #FBBF24; padding: 14px 20px; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
    <i data-lucide="alert-circle" class="w-5 h-5"></i>
    <span>{{ session('warning') }}</span>
  </div>
</div>
@endif

{{-- HERO DO EVENTO --}}
<section class="event-hero"
  @if($event->cover_url) style="background-image: url('{{ $event->cover_url }}')" @endif>
  <div class="event-hero-overlay"></div>
  <div class="event-hero-content">

    {{-- Logo do evento --}}
    @if($event->logo_url)
    <img src="{{ $event->logo_url }}" alt="Logo {{ $event->name }}" class="event-logo">
    @endif

    <span class="event-category-badge">
      {{ \App\Models\Event::CATEGORIES[$event->category] ?? $event->category }}
      @if($event->isPast())
        &nbsp;·&nbsp; REALIZADO
      @endif
    </span>

    <h1 class="event-name">{{ $event->name }}</h1>
    <p class="event-organizer">{{ $event->organizer_name ?? 'Alpha Produções & Faith' }}</p>

    <div class="event-info-pills">
      <div class="info-pill">
        <i data-lucide="calendar" class="w-4 h-4"></i>
        {{ $event->starts_at?->format('l, d \d\e F \d\e Y') ?? $event->date?->format('l, d \d\e F \d\e Y') }}
      </div>
      @if($event->gates_open_at || $event->starts_at)
      <div class="info-pill">
        <i data-lucide="clock" class="w-4 h-4"></i>
        @if($event->gates_open_at) Portões: {{ $event->gates_open_at->format('H\hi') }} @endif
        @if($event->starts_at) · Início: {{ $event->starts_at->format('H\hi') }} @endif
      </div>
      @endif
      <div class="info-pill">
        <i data-lucide="map-pin" class="w-4 h-4"></i>
        {{ $event->venue }}, {{ $event->province ?? $event->city }}
      </div>
    </div>

    {{-- Countdown ou Estado --}}
    @if($event->canBuyTickets() && $event->isUpcoming())
    <div class="event-countdown" id="event-countdown"
         data-target="{{ ($event->starts_at ?? $event->date)?->toIso8601String() }}">
    </div>
    @elseif($event->isPast())
    <div style="margin-bottom: 24px;">
      <span style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px; border-radius: 20px; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #FCA5A5; font-weight: 700; text-transform: uppercase; font-size: 0.85rem;">
        <i data-lucide="check-circle-2" class="w-4 h-4"></i> Evento Realizado em {{ ($event->starts_at ?? $event->date)?->format('d/m/Y') }}
      </span>
    </div>
    @endif

    @if($event->canBuyTickets())
      <a href="{{ route('events.sale', $event->slug ?? $event->id) }}" class="btn-buy-hero">
        <i data-lucide="ticket" class="w-5 h-5"></i>
        COMPRAR BILHETE
      </a>
    @else
      <button type="button" disabled class="btn-buy-hero" style="opacity: 0.75; cursor: not-allowed; background: var(--dark-border); color: var(--text-muted); box-shadow: none;">
        <i data-lucide="lock" class="w-5 h-5"></i>
        VENDAS ENCERRADAS
      </button>
    @endif

  </div>
</section>

{{-- CORPO DA PÁGINA --}}
<div class="event-body container">

  {{-- DESCRIÇÃO --}}
  @if($event->description)
  <section class="event-description glass-card">
    <h2>Sobre o Evento</h2>
    <div class="event-desc-content">
      {!! nl2br(e($event->description)) !!}
    </div>
  </section>
  @endif

  {{-- TIPOS DE BILHETE --}}
  <section class="event-tickets-preview glass-card">
    <h2>Bilhetes Disponíveis</h2>
    @if($event->isPast())
      <div style="text-align: center; padding: 24px 0; color: var(--text-muted);">
        <i data-lucide="calendar-check" class="w-10 h-10" style="margin: 0 auto 12px; color: var(--text-muted); opacity: 0.6;"></i>
        <p style="font-size: 1.1rem; font-weight: 600;">Vendas encerradas para este evento.</p>
        <p style="font-size: 0.85rem; margin-top: 4px;">O evento já foi realizado. Não é possível adquirir novos bilhetes.</p>
      </div>
    @elseif($batches->isEmpty())
      <p style="color: var(--text-muted);">Consulte as opções de bilhetes na página de compra.</p>
    @else
      <div class="tickets-preview-grid">
        @foreach($batches as $batch)
        <div class="ticket-preview-card {{ $batch->available <= 0 ? 'sold-out' : '' }}
                    {{ strtolower($batch->ticket_type) === 'vip' ? 'vip' : '' }}">
          <div class="tpc-name">{{ $batch->display_name }}</div>
          @if($batch->description)
            <div class="tpc-desc">{{ $batch->description }}</div>
          @endif
          <div class="tpc-price">
            {{ number_format($batch->price, 0, ',', '.') }} <span>MT</span>
          </div>
          @if($batch->available > 0 && $batch->available <= 20)
            <div class="tpc-urgency">
              <i data-lucide="alert-triangle" class="w-3 h-3"></i>
              Apenas {{ $batch->available }} restantes!
            </div>
          @endif
          @if($batch->available <= 0)
            <div class="tpc-sold-out">Esgotado</div>
          @endif
        </div>
        @endforeach
      </div>
      @if($event->canBuyTickets())
      <div style="margin-top: 24px; text-align: center;">
        <a href="{{ route('events.sale', $event->slug ?? $event->id) }}" class="btn-gold btn-buy-main">
          <i data-lucide="ticket" class="w-5 h-5"></i>
          COMPRAR BILHETE AGORA
        </a>
      </div>
      @endif
    @endif
  </section>

  {{-- MAPA --}}
  @if($event->maps_url || $event->venue_address)
  <section class="event-location glass-card">
    <h2>Local do Evento</h2>
    <div class="location-info">
      <i data-lucide="map-pin" class="w-6 h-6 text-gold"></i>
      <div>
        <strong>{{ $event->venue }}</strong>
        @if($event->venue_address)
          <p>{{ $event->venue_address }}</p>
        @endif
        @if($event->maps_url)
        <a href="{{ $event->maps_url }}" target="_blank" class="maps-link">
          <i data-lucide="navigation" class="w-4 h-4"></i>
          Ver no Google Maps
        </a>
        @endif
      </div>
    </div>
  </section>
  @endif

  {{-- CONTACTOS --}}
  @if($event->contact_phone || $event->contact_email || $event->event_facebook || $event->event_instagram)
  <section class="event-contact glass-card">
    <h2>Informações</h2>
    <div class="contact-grid">
      @if($event->contact_phone)
      <a href="tel:{{ $event->contact_phone }}" class="contact-item">
        <i data-lucide="phone" class="w-5 h-5"></i>
        <span>{{ $event->contact_phone }}</span>
      </a>
      @endif
      @if($event->contact_email)
      <a href="mailto:{{ $event->contact_email }}" class="contact-item">
        <i data-lucide="mail" class="w-5 h-5"></i>
        <span>{{ $event->contact_email }}</span>
      </a>
      @endif
      @if($event->event_facebook)
      <a href="{{ $event->event_facebook }}" target="_blank" class="contact-item">
        <i data-lucide="facebook" class="w-5 h-5"></i>
        <span>Facebook</span>
      </a>
      @endif
      @if($event->event_instagram)
      <a href="{{ $event->event_instagram }}" target="_blank" class="contact-item">
        <i data-lucide="instagram" class="w-5 h-5"></i>
        <span>Instagram</span>
      </a>
      @endif
    </div>
  </section>
  @endif

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const cdEl = document.getElementById('event-countdown');
  if (!cdEl || !cdEl.dataset.target) return;
  const target = new Date(cdEl.dataset.target);

  function updateCountdown() {
    const diff = target - new Date();
    if (diff <= 0) { cdEl.innerHTML = '<span class="cd-live">AO VIVO AGORA!</span>'; return; }
    const d = Math.floor(diff / 86400000);
    const h = Math.floor((diff % 86400000) / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    cdEl.innerHTML = `
      <div class="cd-unit"><span>${String(d).padStart(2,'0')}</span><small>dias</small></div>
      <div class="cd-sep">:</div>
      <div class="cd-unit"><span>${String(h).padStart(2,'0')}</span><small>horas</small></div>
      <div class="cd-sep">:</div>
      <div class="cd-unit"><span>${String(m).padStart(2,'0')}</span><small>min</small></div>
      <div class="cd-sep">:</div>
      <div class="cd-unit"><span>${String(s).padStart(2,'0')}</span><small>seg</small></div>
    `;
  }
  updateCountdown();
  setInterval(updateCountdown, 1000);
});
</script>
@endpush

@endsection
