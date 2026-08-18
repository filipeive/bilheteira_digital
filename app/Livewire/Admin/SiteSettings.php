<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\SiteSetting;
use App\Models\Ticket;
use App\Services\AuditService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class SiteSettings extends Component
{
    use WithFileUploads;

    // Settings da Plataforma
    public string $portal_name       = 'Alpha Bilheteira';
    public string $hero_label        = '';
    public string $hero_title        = '';
    public string $hero_subtitle     = '';
    public string $announcement_text = '';

    // Contactos & Suporte
    public string $support_phone     = '';
    public string $support_whatsapp  = '';

    // Redes Sociais
    public string $social_facebook  = '';
    public string $social_instagram = '';
    public string $social_tiktok    = '';
    public string $social_youtube   = '';

    // Configurações do PDF do Bilhete
    public string $pdf_event_name_font_size = '20px';
    public string $pdf_brand_text           = '';
    public string $pdf_primary_color        = '#C9A227';
    public bool   $pdf_show_artists         = true;
    public string $pdf_terms_text           = 'Uma vez adquiridos, os bilhetes não podem ser trocados ou reembolsados, a menos que haja cancelamento ou adiamento do evento.';

    // Seleção de Evento
    public ?int $selectedEventId = null;
    public string $event_name        = '';
    public string $event_starts_at   = '';
    public string $event_venue       = '';
    public string $event_city        = '';
    public string $event_province    = '';
    public string $event_description = '';
    public string $event_organizer   = '';

    public string $activeTab = 'platform';

    public function mount(): void
    {
        $all = SiteSetting::all()->pluck('value', 'key');

        $this->portal_name       = $all['portal_name'] ?? 'Alpha Bilheteira';
        $this->hero_label        = $all['hero_label'] ?? '';
        $this->hero_title        = $all['hero_title'] ?? '';
        $this->hero_subtitle     = $all['hero_subtitle'] ?? '';
        $this->announcement_text = $all['announcement_text'] ?? '';

        $this->support_phone    = $all['support_phone'] ?? '';
        $this->support_whatsapp = $all['support_whatsapp'] ?? '';

        $this->social_facebook  = $all['social_facebook'] ?? '';
        $this->social_instagram = $all['social_instagram'] ?? '';
        $this->social_tiktok    = $all['social_tiktok'] ?? '';
        $this->social_youtube   = $all['social_youtube'] ?? '';

        $this->pdf_event_name_font_size = $all['pdf_event_name_font_size'] ?? '20px';
        $this->pdf_brand_text           = $all['pdf_brand_text'] ?? '';
        $this->pdf_primary_color        = $all['pdf_primary_color'] ?? '#C9A227';
        $this->pdf_show_artists         = isset($all['pdf_show_artists']) ? (bool) $all['pdf_show_artists'] : true;
        $this->pdf_terms_text           = $all['pdf_terms_text'] ?? 'Uma vez adquiridos, os bilhetes não podem ser trocados ou reembolsados, a menos que haja cancelamento ou adiamento do evento.';

        // Selecionar primeiro evento ativo por padrão
        $firstEvent = Event::where('is_active', true)->first() ?? Event::first();
        if ($firstEvent) {
            $this->loadEventData($firstEvent->id);
        }
    }

    #[Computed]
    public function events()
    {
        return Event::orderByDesc('is_active')->orderBy('name')->get();
    }

    #[Computed]
    public function sampleTicket()
    {
        return Ticket::first() ?? new Ticket([
            'id' => 'sample-ticket',
            'ticket_code' => 'SAMPLE-001',
            'buyer_name' => 'Exemplo Cliente',
            'ticket_type' => 'promotional',
            'price' => 500,
        ]);
    }

    public function updatedSelectedEventId($value): void
    {
        if ($value) {
            $this->loadEventData((int) $value);
        }
    }

    public function loadEventData(int $eventId): void
    {
        $event = Event::find($eventId);
        if (!$event) return;

        $this->selectedEventId   = $event->id;
        $this->event_name        = $event->name;
        $this->event_starts_at   = $event->starts_at?->format('Y-m-d\TH:i') ?? $event->date?->format('Y-m-d\TH:i') ?? '';
        $this->event_venue       = $event->venue ?? '';
        $this->event_city        = $event->city ?? '';
        $this->event_province    = $event->province ?? '';
        $this->event_description = $event->description ?? '';
        $this->event_organizer   = $event->organizer_name ?? '';
    }

    public function savePlatform(): void
    {
        SiteSetting::set('portal_name', $this->portal_name);
        SiteSetting::set('hero_label', $this->hero_label);
        SiteSetting::set('hero_title', $this->hero_title);
        SiteSetting::set('hero_subtitle', $this->hero_subtitle);
        SiteSetting::set('announcement_text', $this->announcement_text);

        AuditService::log('updated_site_settings', null, [], ['group' => 'platform']);
        $this->dispatch('notify', type: 'success', message: 'Configurações da plataforma guardadas com sucesso.');
    }

    public function savePdf(): void
    {
        SiteSetting::set('pdf_event_name_font_size', $this->pdf_event_name_font_size);
        SiteSetting::set('pdf_brand_text', $this->pdf_brand_text);
        SiteSetting::set('pdf_primary_color', $this->pdf_primary_color);
        SiteSetting::set('pdf_show_artists', $this->pdf_show_artists ? '1' : '0');
        SiteSetting::set('pdf_terms_text', $this->pdf_terms_text);

        AuditService::log('updated_site_settings', null, [], ['group' => 'pdf']);
        $this->dispatch('notify', type: 'success', message: 'Configurações de PDF do bilhete guardadas com sucesso.');
    }

    public function saveEvent(): void
    {
        if (!$this->selectedEventId) {
            $this->dispatch('notify', type: 'error', message: 'Nenhum evento selecionado.');
            return;
        }

        $event = Event::find($this->selectedEventId);
        if ($event) {
            $event->update([
                'name'           => $this->event_name,
                'starts_at'      => $this->event_starts_at ?: null,
                'date'           => $this->event_starts_at ?: null,
                'venue'          => $this->event_venue,
                'city'           => $this->event_city,
                'province'       => $this->event_province,
                'description'    => $this->event_description,
                'organizer_name' => $this->event_organizer,
            ]);

            AuditService::log('updated_event_settings', $event);
            $this->dispatch('notify', type: 'success', message: "Configurações do evento '{$event->name}' guardadas com sucesso.");
        }
    }

    public function saveSocial(): void
    {
        SiteSetting::set('support_phone', $this->support_phone);
        SiteSetting::set('support_whatsapp', $this->support_whatsapp);
        SiteSetting::set('social_facebook', $this->social_facebook);
        SiteSetting::set('social_instagram', $this->social_instagram);
        SiteSetting::set('social_tiktok', $this->social_tiktok);
        SiteSetting::set('social_youtube', $this->social_youtube);

        AuditService::log('updated_site_settings', null, [], ['group' => 'social']);
        $this->dispatch('notify', type: 'success', message: 'Contactos e redes sociais guardados com sucesso.');
    }

    public function render()
    {
        return view('livewire.admin.site-settings')
            ->layout('layouts.admin', ['title' => 'Configurações do Site']);
    }
}
