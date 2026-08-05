<?php

namespace Modules\Marketing\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Marketing\Models\Advertisement;

#[Layout('admin::components.layouts.admin', ['title' => 'Advertisements'])]
class AdvertisementsIndex extends Component
{
    use WithPagination;

    public bool $showForm = false;

    public ?string $editingId = null;

    public array $formData = [];

    public const TYPES = [
        'hero_banner', 'popup', 'sidebar_banner', 'floating_banner', 'announcement_bar',
        'carousel', 'video', 'image', 'sponsored_section', 'countdown_banner',
    ];

    public const PLACEMENTS = [
        'homepage', 'about', 'programs', 'pricing', 'booking', 'blog', 'contact', 'footer', 'sidebar', 'popup',
    ];

    protected function defaultForm(): array
    {
        return [
            'campaign_name' => '', 'type' => 'hero_banner', 'status' => 'draft',
            'title' => '', 'subtitle' => '', 'cta_text' => '', 'description' => '',
            'desktop_image_url' => '', 'cta_link' => '', 'priority' => '0',
            'publish_date' => '', 'expiry_date' => '', 'background_color' => '#0c4a6e',
            'placement' => ['homepage'],
        ];
    }

    public function mount(): void
    {
        $this->formData = $this->defaultForm();
    }

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->formData = $this->defaultForm();
        $this->showForm = true;
    }

    public function openEdit(string $id): void
    {
        $ad = Advertisement::findOrFail($id);
        $this->editingId = $id;
        $this->formData = [
            'campaign_name' => $ad->campaign_name,
            'type' => $ad->type,
            'status' => $ad->status,
            'title' => $ad->title ?? '',
            'subtitle' => $ad->subtitle ?? '',
            'cta_text' => $ad->cta_text ?? '',
            'description' => $ad->description ?? '',
            'desktop_image_url' => $ad->desktop_image_url ?? '',
            'cta_link' => $ad->cta_link ?? '',
            'priority' => (string) $ad->priority,
            'publish_date' => $ad->publish_date?->format('Y-m-d\TH:i') ?? '',
            'expiry_date' => $ad->expiry_date?->format('Y-m-d\TH:i') ?? '',
            'background_color' => $ad->background_color ?? '#0c4a6e',
            'placement' => $ad->placement ?: ['homepage'],
        ];
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'formData.campaign_name' => 'required|string',
        ], [
            'formData.campaign_name.required' => 'Campaign name required',
        ]);

        $payload = [
            'campaign_name' => $this->formData['campaign_name'],
            'type' => $this->formData['type'],
            'status' => $this->formData['status'],
            'title' => $this->formData['title'] ?: null,
            'subtitle' => $this->formData['subtitle'] ?: null,
            'cta_text' => $this->formData['cta_text'] ?: null,
            'description' => $this->formData['description'] ?: null,
            'desktop_image_url' => $this->formData['desktop_image_url'] ?: null,
            'cta_link' => $this->formData['cta_link'] ?: null,
            'priority' => (int) $this->formData['priority'],
            'publish_date' => $this->formData['publish_date'] ?: null,
            'expiry_date' => $this->formData['expiry_date'] ?: null,
            'background_color' => $this->formData['background_color'] ?: null,
            'placement' => $this->formData['placement'] ?: ['homepage'],
        ];

        if ($this->editingId) {
            Advertisement::findOrFail($this->editingId)->update($payload);
        } else {
            Advertisement::create($payload);
        }

        $this->showForm = false;
    }

    public function toggleStatus(string $id): void
    {
        $ad = Advertisement::findOrFail($id);

        if (! in_array($ad->status, ['active', 'paused'], true)) {
            return;
        }

        $ad->update(['status' => $ad->status === 'active' ? 'paused' : 'active']);
    }

    public function delete(string $id): void
    {
        Advertisement::findOrFail($id)->delete();
    }

    public function render()
    {
        // Stat tiles are computed via independent aggregate queries, not
        // from the paginated $ads collection below, so they keep reflecting
        // the whole table rather than just the current page.
        return view('marketing::livewire.admin.advertisements-index', [
            'ads' => Advertisement::orderByDesc('priority')->paginate(20),
            'activeCount' => Advertisement::where('status', 'active')->count(),
            'draftCount' => Advertisement::where('status', 'draft')->count(),
            'totalViews' => (int) Advertisement::sum('view_count'),
            'totalClicks' => (int) Advertisement::sum('click_count'),
            'types' => self::TYPES,
            'placements' => self::PLACEMENTS,
        ]);
    }
}
