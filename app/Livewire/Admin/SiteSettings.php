<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use App\Services\AuditService;
use Livewire\Component;
use Livewire\WithFileUploads;

class SiteSettings extends Component
{
    use WithFileUploads;

    public string $event_name        = '';
    public string $event_date        = '';
    public string $event_time        = '';
    public string $event_venue       = '';
    public string $event_city        = '';
    public string $event_description = '';
    public string $event_contact_1   = '';
    public string $event_contact_2   = '';

    public string $social_facebook  = '';
    public string $social_instagram = '';
    public string $social_tiktok    = '';
    public string $social_youtube   = '';
    public string $social_whatsapp  = '';

    public string $banner_title    = '';
    public string $banner_subtitle = '';
    public $banner_image = null;

    public string $activeTab = 'event';

    public function mount(): void
    {
        $all = SiteSetting::all()->pluck('value', 'key');

        $this->event_name        = $all['event_name'] ?? '';
        $this->event_date        = $all['event_date'] ?? '';
        $this->event_time        = $all['event_time'] ?? '';
        $this->event_venue       = $all['event_venue'] ?? '';
        $this->event_city        = $all['event_city'] ?? '';
        $this->event_description = $all['event_description'] ?? '';
        $this->event_contact_1   = $all['event_contact_1'] ?? '';
        $this->event_contact_2   = $all['event_contact_2'] ?? '';

        $this->social_facebook  = $all['social_facebook'] ?? '';
        $this->social_instagram = $all['social_instagram'] ?? '';
        $this->social_tiktok    = $all['social_tiktok'] ?? '';
        $this->social_youtube   = $all['social_youtube'] ?? '';
        $this->social_whatsapp  = $all['social_whatsapp'] ?? '';

        $this->banner_title    = $all['banner_title'] ?? '';
        $this->banner_subtitle = $all['banner_subtitle'] ?? '';
    }

    public function saveEvent(): void
    {
        $fields = ['name', 'date', 'time', 'venue', 'city', 'description', 'contact_1', 'contact_2'];
        foreach ($fields as $field) {
            SiteSetting::set("event_{$field}", $this->{"event_{$field}"});
        }
        AuditService::log('updated_site_settings', null, [], ['group' => 'event']);
        $this->dispatch('notify', type: 'success', message: 'Informações do evento guardadas.');
    }

    public function saveSocial(): void
    {
        $fields = ['facebook', 'instagram', 'tiktok', 'youtube', 'whatsapp'];
        foreach ($fields as $field) {
            SiteSetting::set("social_{$field}", $this->{"social_{$field}"});
        }
        AuditService::log('updated_site_settings', null, [], ['group' => 'social']);
        $this->dispatch('notify', type: 'success', message: 'Redes sociais guardadas.');
    }

    public function saveBanner(): void
    {
        SiteSetting::set('banner_title', $this->banner_title);
        SiteSetting::set('banner_subtitle', $this->banner_subtitle);
        if ($this->banner_image) {
            $path = $this->banner_image->store('banners', 'public');
            SiteSetting::set('banner_image', $path);
        }
        AuditService::log('updated_site_settings', null, [], ['group' => 'banner']);
        $this->dispatch('notify', type: 'success', message: 'Banner guardado.');
    }

    public function render()
    {
        return view('livewire.admin.site-settings')
            ->layout('layouts.admin', ['title' => 'Configurações do Site']);
    }
}
