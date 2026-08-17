@extends('layouts.public')

@section('title', 'Comprar Bilhete — ' . $event->name)

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

  {{-- Componente Livewire — passa o evento --}}
  @livewire('ticket-form', ['event' => $event])
</div>

@endsection
