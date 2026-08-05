<?php

namespace Modules\Cms\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\Cms\Events\SiteSettingsUpdated;
use Modules\Cms\Models\SiteSetting;
use Modules\Core\Support\RoleGroups;

#[Layout('admin::components.layouts.admin', ['title' => 'Settings'])]
class SiteSettingsForm extends Component
{
    public string $activeGroup = 'general';

    public array $values = [];

    public bool $saving = false;

    public ?string $savedMessage = null;

    /**
     * @return array<int, array{key: string, label: string, fields: array<int, array{key: string, label: string, type: string, description?: string}>}>
     */
    protected function groups(): array
    {
        return [
            ['key' => 'general', 'label' => 'General', 'fields' => [
                ['key' => 'site_name', 'label' => 'Site Name', 'type' => 'text'],
                ['key' => 'site_tagline', 'label' => 'Tagline', 'type' => 'text'],
                ['key' => 'site_description', 'label' => 'Description', 'type' => 'textarea'],
            ]],
            ['key' => 'homepage', 'label' => 'Homepage', 'fields' => [
                ['key' => 'hero_title', 'label' => 'Hero Title', 'type' => 'text'],
                ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
                ['key' => 'hero_cta_primary', 'label' => 'Primary CTA Text', 'type' => 'text'],
                ['key' => 'hero_cta_secondary', 'label' => 'Secondary CTA Text', 'type' => 'text'],
                ['key' => 'hero_image_url', 'label' => 'Hero Image', 'type' => 'image', 'description' => 'Homepage background image'],
            ]],
            ['key' => 'contact', 'label' => 'Contact', 'fields' => [
                ['key' => 'contact_email', 'label' => 'Contact Email', 'type' => 'email'],
                ['key' => 'contact_phone', 'label' => 'Phone Number', 'type' => 'text'],
                ['key' => 'contact_whatsapp', 'label' => 'WhatsApp Number', 'type' => 'text', 'description' => 'Include country code, no spaces (e.g. 254700123456)'],
                ['key' => 'contact_address', 'label' => 'Office Address', 'type' => 'text'],
            ]],
            ['key' => 'social', 'label' => 'Social Media', 'fields' => [
                ['key' => 'social_facebook', 'label' => 'Facebook URL', 'type' => 'url'],
                ['key' => 'social_twitter', 'label' => 'Twitter/X URL', 'type' => 'url'],
                ['key' => 'social_instagram', 'label' => 'Instagram URL', 'type' => 'url'],
                ['key' => 'social_linkedin', 'label' => 'LinkedIn URL', 'type' => 'url'],
                ['key' => 'social_youtube', 'label' => 'YouTube URL', 'type' => 'url'],
                ['key' => 'social_tiktok', 'label' => 'TikTok URL', 'type' => 'url'],
            ]],
            ['key' => 'branding', 'label' => 'Branding', 'fields' => [
                ['key' => 'primary_color', 'label' => 'Primary Color', 'type' => 'color'],
                ['key' => 'secondary_color', 'label' => 'Secondary Color', 'type' => 'color'],
                ['key' => 'accent_color', 'label' => 'Accent Color', 'type' => 'color'],
            ]],
            ['key' => 'analytics', 'label' => 'Analytics', 'fields' => [
                ['key' => 'google_analytics_id', 'label' => 'Google Analytics ID', 'type' => 'text', 'description' => 'Your GA4 Measurement ID (G-XXXXXXXXXX)'],
                ['key' => 'meta_pixel_id', 'label' => 'Meta Pixel ID', 'type' => 'text', 'description' => 'Your Facebook/Meta Pixel ID'],
            ]],
            ['key' => 'system', 'label' => 'System', 'fields' => [
                ['key' => 'maintenance_mode', 'label' => 'Maintenance Mode', 'type' => 'select', 'description' => 'When enabled, the site shows a maintenance page'],
            ]],
        ];
    }

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasRole(RoleGroups::ADMIN), 403);

        $existing = SiteSetting::pluck('value', 'key');

        foreach ($this->groups() as $group) {
            foreach ($group['fields'] as $field) {
                $this->values[$field['key']] = $existing[$field['key']] ?? '';
            }
        }
    }

    public function save(): void
    {
        $this->saving = true;

        $before = SiteSetting::pluck('value', 'key');
        $changedKeys = [];

        foreach ($this->groups() as $group) {
            foreach ($group['fields'] as $field) {
                $newValue = $this->values[$field['key']] ?? '';

                if (($before[$field['key']] ?? '') !== $newValue) {
                    $changedKeys[] = $field['key'];
                }

                SiteSetting::updateOrCreate(
                    ['key' => $field['key']],
                    [
                        'value' => $newValue,
                        'category' => $group['key'],
                        'label' => $field['label'],
                        'description' => $field['description'] ?? null,
                        'updated_at' => now(),
                    ],
                );
            }
        }

        if ($changedKeys) {
            SiteSettingsUpdated::dispatch(auth()->id(), $changedKeys, request()->ip(), request()->userAgent());
        }

        $this->saving = false;
        $this->savedMessage = 'Settings saved successfully!';
    }

    public function render()
    {
        return view('cms::livewire.admin.site-settings-form', [
            'groups' => $this->groups(),
        ]);
    }
}
