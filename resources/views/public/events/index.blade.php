@extends('layouts.public')

@section('title', 'Alpha Bilheteira — Eventos Gospel em Moçambique')

@section('content')

{{-- HERO DA PLATAFORMA --}}
<section class="platform-hero">
  <div class="platform-hero-bg" @if(!empty($settings['hero_image'])) style="background-image: url('{{ asset(ltrim($settings['hero_image'], '/')) }}'); background-size: cover; background-position: center; opacity: 0.25;" @endif></div>
  <div class="platform-hero-content">

    @if(!empty($settings['announcement_text']))
      <div style="background: rgba(212,160,23,0.12); border: 1px solid rgba(212,160,23,0.3); border-radius: 20px; padding: 6px 18px; color: var(--gold); font-size: 0.82rem; font-weight: 600; margin-bottom: 16px; display: inline-flex; align-items: center; gap: 8px;">
        <i data-lucide="sparkles" class="w-4 h-4"></i> {{ $settings['announcement_text'] }}
      </div>
    @else
      <div class="platform-badge">{{ $settings['hero_label'] ?? 'Moçambique' }}</div>
    @endif

    <h1 class="platform-title">
      {!! !empty($settings['hero_title']) ? e($settings['hero_title']) : 'EVENTOS <span>GOSPEL</span><br>EM MOÇAMBIQUE' !!}
    </h1>
    <p class="platform-sub">
      {{ $settings['hero_subtitle'] ?? 'Compre bilhetes oficiais para concertos, conferências e eventos cristãos em Moçambique com M-Pesa e e-Mola.' }}
    </p>

    {{-- PESQUISA --}}
    <form method="GET" action="{{ route('events.index') }}" class="platform-search">
      <input type="text" name="search" value="{{ request('search') }}"
             placeholder="Pesquisar evento, artista, local ou província..."
             class="search-input">
      <button type="submit" class="search-btn">
        <i data-lucide="search" class="w-5 h-5"></i>
        Pesquisar
      </button>
    </form>
  </div>
</section>

{{-- FILTROS --}}
<section class="catalog-filters-section">
  <div class="catalog-filters-wrap">

    {{-- Categorias --}}
    <div class="filter-pills">
      <a href="{{ route('events.index') }}"
         class="pill {{ !request('category') ? 'active' : '' }}">
        Todos
      </a>
      @foreach(\App\Models\Event::CATEGORIES as $key => $label)
      <a href="{{ route('events.index', ['category' => $key]) }}"
         class="pill {{ request('category') === $key ? 'active' : '' }}">
        {{ $label }}
      </a>
      @endforeach
    </div>

    {{-- Províncias --}}
    <select name="province" onchange="window.location='{{ route('events.index') }}?province='+this.value"
            class="filter-select">
      <option value="">Todas as Províncias</option>
      @foreach(\App\Models\Event::PROVINCES as $key => $label)
      <option value="{{ $key }}" {{ request('province') === $key ? 'selected' : '' }}>
        {{ $label }}
      </option>
      @endforeach
    </select>

  </div>
</section>

{{-- EVENTO EM DESTAQUE --}}
@if($featured && !request()->anyFilled(['search', 'category', 'province']))
<section class="featured-section">
  <div class="featured-label">
    <i data-lucide="star" class="w-4 h-4"></i> Em Destaque
  </div>
  <div class="featured-card" @if($featured->cover_url) style="background-image: url('{{ $featured->cover_url }}')" @endif>
    <div class="featured-overlay">
      <div class="featured-content">
        <span class="featured-category">{{ \App\Models\Event::CATEGORIES[$featured->category] ?? $featured->category }}</span>
        <h2 class="featured-name">{{ $featured->name }}</h2>
        <p class="featured-meta">
          <i data-lucide="calendar" class="w-4 h-4"></i>
          {{ $featured->starts_at?->format('d \d\e F \d\e Y') ?? $featured->date?->format('d \d\e F \d\e Y') }}
          &nbsp;·&nbsp;
          <i data-lucide="map-pin" class="w-4 h-4"></i>
          {{ $featured->venue }}, {{ $featured->province ?? 'Zambézia' }}
        </p>
        <a href="{{ route('events.show', $featured->slug ?? $featured->id) }}" class="featured-cta">
          <i data-lucide="ticket" class="w-5 h-5"></i>
          Ver Bilhetes
        </a>
      </div>
    </div>
  </div>
</section>
@endif

{{-- GRID DE EVENTOS --}}
<section class="catalog-section">
  @if($events->isEmpty())
    <div class="catalog-empty">
      <i data-lucide="calendar-x" class="w-12 h-12"></i>
      <p>Nenhum evento encontrado.</p>
      <a href="{{ route('events.index') }}">Ver todos os eventos</a>
    </div>
  @else
    <div class="events-grid">
      @foreach($events as $event)
      <article class="event-card">

        {{-- Imagem de capa --}}
        <a href="{{ route('events.show', $event->slug ?? $event->id) }}" class="event-card-img-wrap">
          @if($event->cover_url)
            <img src="{{ $event->cover_url }}" alt="{{ $event->name }}" class="event-card-img" loading="lazy">
          @else
            <div class="event-card-img-placeholder">
              <i data-lucide="music-2" class="w-12 h-12"></i>
            </div>
          @endif
          <span class="event-card-category">
            {{ \App\Models\Event::CATEGORIES[$event->category] ?? $event->category }}
          </span>
          <!-- Se passou a data entao deve vir realizado -->
          @if($event->starts_at?->isPast())
            <span class="event-card-badge sold-out">Realizado</span>           
          @elseif($event->status === 'sold_out')
            <span class="event-card-badge sold-out">Esgotado</span>
          @elseif($event->isUpcoming() && $event->starts_at?->diffInDays() <= 7)
            <span class="event-card-badge soon">Em breve</span>
          @endif
        </a>

        {{-- Info --}}
        <div class="event-card-body">
          <h3 class="event-card-name">
            <a href="{{ route('events.show', $event->slug ?? $event->id) }}">{{ $event->name }}</a>
          </h3>
          <p class="event-card-organizer">{{ $event->organizer_name ?? 'Alpha Produções' }}</p>
          <div class="event-card-meta">
            <span>
              <i data-lucide="calendar" class="w-3 h-3"></i>
              {{ $event->starts_at?->format('d M Y') ?? ($event->date?->format('d M Y') ?? '—') }}
            </span>
            <span>
              <i data-lucide="map-pin" class="w-3 h-3"></i>
              {{ $event->venue }}, {{ $event->province ?? $event->city }}
            </span>
          </div>

          {{-- Preço mínimo --}}
          @php
            $minPrice = $event->batches()->where('is_active', true)->min('price');
          @endphp
          @if($minPrice !== null)
          <div class="event-card-price">
            A partir de <strong>{{ number_format($minPrice, 0, ',', '.') }} MT</strong>
          </div>
          @endif
<!--
          <a href="{{ route('events.sale', $event->slug ?? $event->id) }}"
             class="event-card-btn {{ $event->status === 'sold_out' ? 'disabled' : '' }}">
            @if($event->status === 'sold_out')
              Esgotado
            @else
              <i data-lucide="ticket" class="w-4 h-4"></i> Comprar Bilhete
            @endif
          </a>
        </div>

      </article>
      @endforeach
    </div>

    {{-- Paginação --}}
    @if($events->hasPages())
    <div class="pagination-wrap">
      {{ $events->withQueryString()->links() }}
    </div>
    @endif
  @endif
</section>

{{-- CTA PROMOTORES --}}
<section class="promoter-cta-section">
  <div class="promoter-cta-content">
    <i data-lucide="megaphone" class="w-8 h-8"></i>
    <h3>Tens um evento gospel?</h3>
    <p>Cria o teu evento e vende bilhetes online com M-Pesa e e-Mola em Moçambique.</p>
    <a href="https://wa.me/258875411644?text=Olá! Quero publicar um evento na Alpha Bilheteira."
       target="_blank" class="btn-whatsapp">
      <i data-lucide="message-circle" class="w-5 h-5"></i>
      Falar connosco no WhatsApp
    </a>
  </div>
</section>

@endsection
