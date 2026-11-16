<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Services\AuditService;
use Livewire\Component;
use Livewire\WithFileUploads;

class EventManager extends Component
{
    use WithFileUploads;

    public ?int $editingId = null;
    public bool $isCreating = false;

    // Fields
    public string $name = '';
    public string $slug = '';
    public string $category = 'gospel';
    public string $province = 'Zambézia';
    public string $venue = '';
    public string $venue_address = '';
    public string $maps_url = '';
    public string $organizer_name = 'Alpha Produções & Faith';
    public string $ticket_theme = 'gold';
    public string $primary_color = '#D4A017';
    public string $secondary_color = '#0D0B07';
    public string $starts_at = '';
    public string $ends_at = '';
    public string $gates_open_at = '';
    public bool $tbd_date = false;
    public string $description = '';
    public int $max_capacity = 0;
    public bool $featured = false;
    public string $status = 'published';
    public bool $is_active = true;

    // Contact & SEO
    public string $contact_phone = '';
    public string $contact_email = '';
    public string $event_website = '';
    public string $event_facebook = '';
    public string $event_instagram = '';
    public string $meta_title = '';
    public string $meta_description = '';

    // PDF / Ticket customization
    public string $artists = '';
    public string $pdf_brand_text = '';
    public string $pdf_terms_text = '';

    // Uploads
    public $cover_image_file;
    public $logo_image_file;
    public $banner_image_file;
    public $upload_ticket_bg_image;
    public $upload_artist_photo_1;
    public $upload_artist_photo_2;

    protected function rules(): array
    {
        return [
            'name'           => 'required|string|min:3|max:150',
            'category'       => 'required|string',
            'province'       => 'required|string',
            'venue'          => 'required|string|max:150',
            'organizer_name' => 'required|string|max:150',
            'starts_at'      => $this->tbd_date ? 'nullable|date' : 'required|date',
            'status'         => 'required|string|in:draft,published,cancelled,ended,sold_out',
            'ticket_theme'   => 'required|string',
            'contact_phone'  => 'nullable|string|max:40',
            'cover_image_file'       => 'nullable|image|max:4096',
            'logo_image_file'        => 'nullable|image|max:2048',
            'banner_image_file'      => 'nullable|image|max:4096',
            'upload_ticket_bg_image' => 'nullable|image|max:4096',
            'upload_artist_photo_1'  => 'nullable|image|max:2048',
            'upload_artist_photo_2'  => 'nullable|image|max:2048',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->isCreating = true;
    }

    public function edit(int $id): void
    {
        $event = Event::findOrFail($id);
        $this->editingId       = $event->id;
        $this->isCreating      = false;
        $this->name            = $event->name;
        $this->slug            = $event->slug ?? '';
        $this->category        = $event->category ?? 'gospel';
        $this->province        = $event->province ?? 'Zambézia';
        $this->venue           = $event->venue ?? '';
        $this->venue_address   = $event->venue_address ?? '';
        $this->maps_url        = $event->maps_url ?? '';
        $this->organizer_name  = $event->organizer_name ?? 'Alpha Produções & Faith';
        $this->ticket_theme    = $event->ticket_theme ?? 'gold';
        $this->primary_color   = $event->primary_color ?? '#D4A017';
        $this->secondary_color = $event->secondary_color ?? '#0D0B07';
        $this->starts_at       = $event->starts_at?->format('Y-m-d\TH:i') ?? $event->date?->format('Y-m-d\TH:i') ?? '';
        $this->ends_at         = $event->ends_at?->format('Y-m-d\TH:i') ?? '';
        $this->gates_open_at   = $event->gates_open_at?->format('Y-m-d\TH:i') ?? '';
        $this->tbd_date        = $event->isTbd();
        $this->description     = $event->description ?? '';
        $this->max_capacity    = $event->max_capacity ?? 0;
        $this->featured        = $event->featured ?? false;
        $this->status          = $event->status ?? 'published';
        $this->is_active       = $event->is_active ?? true;
        $this->contact_phone   = $event->contact_phone ?? '';
        $this->contact_email   = $event->contact_email ?? '';
        $this->event_website   = $event->event_website ?? '';
        $this->event_facebook  = $event->event_facebook ?? '';
        $this->event_instagram = $event->event_instagram ?? '';
        $this->meta_title      = $event->meta_title ?? '';
        $this->meta_description= $event->meta_description ?? '';
        $this->artists         = $event->artists ?? '';
        $this->pdf_brand_text  = $event->pdf_brand_text ?? '';
        $this->pdf_terms_text  = $event->pdf_terms_text ?? '';
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'            => $this->name,
            'category'        => $this->category,
            'province'        => $this->province,
            'venue'           => $this->venue,
            'city'            => $this->province,
            'venue_address'   => $this->venue_address ?: null,
            'maps_url'        => $this->maps_url ?: null,
            'organizer_name'  => $this->organizer_name,
            'ticket_theme'    => $this->ticket_theme,
            'primary_color'   => $this->primary_color ?: '#D4A017',
            'secondary_color' => $this->secondary_color ?: '#0D0B07',
            'starts_at'       => ($this->tbd_date || !$this->starts_at) ? null : $this->starts_at,
            'date'            => ($this->tbd_date || !$this->starts_at) ? null : $this->starts_at,
            'ends_at'         => ($this->tbd_date || !$this->ends_at) ? null : $this->ends_at,
            'gates_open_at'   => ($this->tbd_date || !$this->gates_open_at) ? null : $this->gates_open_at,
            'description'     => $this->description ?: null,
            'max_capacity'    => $this->max_capacity,
            'capacity'        => $this->max_capacity,
            'featured'        => $this->featured,
            'status'          => $this->status,
            'is_active'       => $this->status === 'published',
            'contact_phone'   => $this->contact_phone ?: null,
            'contact_email'   => $this->contact_email ?: null,
            'event_website'   => $this->event_website ?: null,
            'event_facebook'  => $this->event_facebook ?: null,
            'event_instagram' => $this->event_instagram ?: null,
            'meta_title'      => $this->meta_title ?: null,
            'meta_description'=> $this->meta_description ?: null,
            'artists'         => $this->artists ?: null,
            'pdf_brand_text'  => $this->pdf_brand_text ?: null,
            'pdf_terms_text'  => $this->pdf_terms_text ?: null,
        ];

        if ($this->cover_image_file) {
            $data['cover_image'] = $this->cover_image_file->store('events/covers', 'public');
        }
        if ($this->logo_image_file) {
            $data['logo_image'] = $this->logo_image_file->store('events/logos', 'public');
        }
        if ($this->banner_image_file) {
            $data['banner_image'] = $this->banner_image_file->store('events/banners', 'public');
        }
        if ($this->upload_ticket_bg_image) {
            $filenameBg = 'ticket_bg_ev' . ($this->editingId ?? time()) . '_' . time() . '.' . $this->upload_ticket_bg_image->getClientOriginalExtension();
            $this->upload_ticket_bg_image->storeAs('images', $filenameBg, 'public');
            $data['ticket_bg_image'] = 'storage/images/' . $filenameBg;
        }
        if ($this->upload_artist_photo_1) {
            $filename1 = 'artist_1_ev' . ($this->editingId ?? time()) . '_' . time() . '.' . $this->upload_artist_photo_1->getClientOriginalExtension();
            $this->upload_artist_photo_1->storeAs('artists', $filename1, 'public');
            $data['artist_photo_1'] = 'storage/artists/' . $filename1;
        }
        if ($this->upload_artist_photo_2) {
            $filename2 = 'artist_2_ev' . ($this->editingId ?? time()) . '_' . time() . '.' . $this->upload_artist_photo_2->getClientOriginalExtension();
            $this->upload_artist_photo_2->storeAs('artists', $filename2, 'public');
            $data['artist_photo_2'] = 'storage/artists/' . $filename2;
        }

        if ($this->editingId) {
            $event = Event::findOrFail($this->editingId);
            $event->update($data);
            AuditService::log('updated_event', $event);
            $this->dispatch('notify', type: 'success', message: 'Evento actualizado com sucesso.');
        } else {
            $event = Event::create($data);
            AuditService::log('created_event', $event);
            $this->dispatch('notify', type: 'success', message: 'Novo evento criado com sucesso.');
        }

        $this->resetForm();
    }

    public function toggleStatus(int $id): void
    {
        $event = Event::findOrFail($id);
        $newStatus = $event->status === 'published' ? 'draft' : 'published';
        $event->update([
            'status' => $newStatus,
            'is_active' => $newStatus === 'published'
        ]);
        AuditService::log('toggled_event_status', $event);
        $this->dispatch('notify', type: 'success', message: 'Estado do evento alterado.');
    }

    public function toggleFeatured(int $id): void
    {
        $event = Event::findOrFail($id);
        $event->update(['featured' => !$event->featured]);
        $this->dispatch('notify', type: 'success', message: 'Destaque do evento alterado.');
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingId', 'isCreating', 'name', 'slug', 'category', 'province',
            'venue', 'venue_address', 'maps_url', 'organizer_name', 'ticket_theme',
            'primary_color', 'secondary_color', 'starts_at', 'ends_at', 'gates_open_at', 'tbd_date',
            'description', 'max_capacity', 'featured', 'status', 'is_active',
            'contact_phone', 'contact_email', 'event_website', 'event_facebook',
            'event_instagram', 'meta_title', 'meta_description',
            'artists', 'pdf_brand_text', 'pdf_terms_text',
            'cover_image_file', 'logo_image_file', 'banner_image_file',
            'upload_ticket_bg_image', 'upload_artist_photo_1', 'upload_artist_photo_2',
        ]);
        $this->category = 'gospel';
        $this->province = 'Zambézia';
        $this->ticket_theme = 'gold';
        $this->status = 'published';
    }

    public function render()
    {
        return view('livewire.admin.event-manager', [
            'events' => Event::orderByDesc('featured')->orderByDesc('created_at')->get(),
            'categories' => Event::CATEGORIES,
            'provinces' => Event::PROVINCES,
            'themes' => Event::THEMES,
            'statuses' => Event::STATUS,
        ])->layout('layouts.admin', ['title' => 'Gestão de Eventos']);
    }
}
