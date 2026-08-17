<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventCatalogController extends Controller
{
    /**
     * Listagem pública de eventos.
     */
    public function index(Request $request)
    {
        $events = Event::query()
            ->where('status', 'published')
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->when($request->province,  fn($q) => $q->where('province', $request->province))
            ->when($request->search,    fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderByDesc('featured')
            ->orderBy('starts_at')
            ->paginate(12);

        $categories = Event::CATEGORIES;
        $provinces  = Event::PROVINCES;
        $featured   = Event::where('status', 'published')
                           ->where('featured', true)
                           ->orderBy('starts_at')
                           ->first();

        return view('public.events.index', compact('events', 'categories', 'provinces', 'featured'));
    }

    /**
     * Página de detalhe de um evento.
     */
    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)
                      ->orWhere('id', $slug)
                      ->where('status', '!=', 'draft')
                      ->firstOrFail();

        $batches = $event->batches()
                         ->where('is_active', true)
                         ->orderBy('sort_order')
                         ->get();

        // SEO dinâmico
        $meta = [
            'title'       => $event->meta_title ?? $event->name . ' — Bilhetes Online',
            'description' => $event->meta_description ?? Str::limit($event->description ?? '', 160),
            'image'       => $event->cover_url ?? asset('images/og-default.jpg'),
            'url'         => $event->public_url,
        ];

        return view('public.events.show', compact('event', 'batches', 'meta'));
    }

    /**
     * Página de compra de bilhetes (delega ao Livewire).
     */
    public function sale(string $slug)
    {
        $event = Event::where('slug', $slug)
                      ->orWhere('id', $slug)
                      ->where('status', 'published')
                      ->firstOrFail();

        return view('public.events.sale', compact('event'));
    }
}
