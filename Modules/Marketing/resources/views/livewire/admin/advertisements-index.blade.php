@php
    $statusColors = [
        'draft' => 'bg-gray-100 text-gray-600',
        'active' => 'bg-green-100 text-green-700',
        'paused' => 'bg-yellow-100 text-yellow-700',
        'archived' => 'bg-gray-100 text-gray-500',
        'expired' => 'bg-red-100 text-red-600',
    ];
@endphp
<div class="p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-black flex items-center gap-2"><x-core::icon name="megaphone" class="w-6 h-6 text-brand-500" /> Advertisements</h1>
        <p class="text-sm text-muted-foreground">{{ $ads->total() }} campaigns · {{ $activeCount }} active</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-card border border-border rounded-2xl p-4">
            <p class="text-2xl font-black text-green-600">{{ $activeCount }}</p>
            <p class="text-xs text-muted-foreground">Active</p>
        </div>
        <div class="bg-card border border-border rounded-2xl p-4">
            <p class="text-2xl font-black text-gray-500">{{ $draftCount }}</p>
            <p class="text-xs text-muted-foreground">Draft</p>
        </div>
        <div class="bg-card border border-border rounded-2xl p-4">
            <p class="text-2xl font-black text-brand-600">{{ number_format($totalViews) }}</p>
            <p class="text-xs text-muted-foreground">Total Views</p>
        </div>
        <div class="bg-card border border-border rounded-2xl p-4">
            <p class="text-2xl font-black text-blue-600">{{ number_format($totalClicks) }}</p>
            <p class="text-xs text-muted-foreground">Total Clicks</p>
        </div>
    </div>

    <div class="flex justify-end">
        <button wire:click="openCreate" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">
            <x-core::icon name="plus" class="w-4 h-4" /> New Campaign
        </button>
    </div>

    <div class="space-y-3">
        @forelse ($ads as $ad)
            <div wire:key="ad-{{ $ad->id }}" class="bg-card border border-border rounded-2xl p-4 flex gap-4">
                <div class="w-24 h-16 rounded-xl flex-shrink-0 bg-cover bg-center flex items-center justify-center"
                     style="background-color: {{ $ad->background_color ?? '#0c4a6e' }}; @if($ad->desktop_image_url) background-image: url('{{ $ad->desktop_image_url }}'); @endif">
                    @if (! $ad->desktop_image_url)
                        <x-core::icon name="image" class="w-6 h-6 text-white/30" />
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-semibold text-sm truncate">{{ $ad->campaign_name }}</p>
                            @if ($ad->title)
                                <p class="text-xs text-muted-foreground truncate">{{ $ad->title }}</p>
                            @endif
                        </div>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full capitalize flex-shrink-0 {{ $statusColors[$ad->status] ?? $statusColors['draft'] }}">{{ $ad->status }}</span>
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2 text-xs text-muted-foreground">
                        <span>Type: {{ str_replace('_', ' ', $ad->type) }}</span>
                        <span>Priority: {{ $ad->priority }}</span>
                        <span>Placements: {{ implode(', ', $ad->placement ?? []) }}</span>
                        <span>Views: {{ number_format($ad->view_count) }}</span>
                        <span>Clicks: {{ number_format($ad->click_count) }}</span>
                        @if ($ad->expiry_date)
                            <span>Expires: {{ $ad->expiry_date->format('d M Y') }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-start gap-1 flex-shrink-0">
                    @if (in_array($ad->status, ['active', 'paused'], true))
                        <button wire:click="toggleStatus('{{ $ad->id }}')" class="p-1.5 hover:bg-muted rounded-lg transition-colors" title="{{ $ad->status === 'active' ? 'Pause' : 'Activate' }}">
                            <x-core::icon :name="$ad->status === 'active' ? 'eye-off' : 'eye'" class="w-4 h-4 {{ $ad->status === 'active' ? 'text-orange-500' : 'text-green-600' }}" />
                        </button>
                    @endif
                    <button wire:click="openEdit('{{ $ad->id }}')" class="p-1.5 hover:bg-muted rounded-lg transition-colors text-muted-foreground hover:text-foreground" title="Edit">
                        <x-core::icon name="edit" class="w-3.5 h-3.5" />
                    </button>
                    <button wire:click="delete('{{ $ad->id }}')" wire:confirm="Delete this advertisement?" class="p-1.5 hover:bg-red-50 rounded-lg transition-colors text-muted-foreground hover:text-red-600" title="Delete">
                        <x-core::icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        @empty
            <div class="py-16 text-center">
                <x-core::icon name="megaphone" class="w-14 h-14 text-muted-foreground/30 mx-auto mb-4" />
                <p class="font-semibold text-muted-foreground">No advertisements yet</p>
                <p class="text-sm text-muted-foreground">Create your first campaign with AI-powered ad copy</p>
            </div>
        @endforelse
    </div>

    @if ($ads->hasPages())
        <div>{{ $ads->links() }}</div>
    @endif

    @if ($showForm)
        <div
            x-data="{
                variations: [],
                generating: false,
                generate() {
                    this.generating = true;
                    setTimeout(() => {
                        const name = ($wire.formData.campaign_name || '').toLowerCase();
                        let bucket = 'generic';
                        if (name.includes('event') || name.includes('webinar')) bucket = 'event';
                        else if (name.includes('sale') || name.includes('discount') || name.includes('offer') || name.includes('early bird')) bucket = 'sale';
                        else if (name.includes('cohort') || name.includes('intake') || name.includes('batch')) bucket = 'cohort';
                        else if (name.includes('free') || name.includes('trial')) bucket = 'free';

                        const templates = {
                            event: [
                                { title: 'Join Our Live Training Event', subtitle: 'Limited seats available', description: 'Reserve your spot at this exclusive session and level up your skills.', cta_text: 'Reserve My Seat' },
                                { title: 'Don\\'t Miss This Event', subtitle: 'Learn from industry experts', description: 'An interactive session designed to fast-track your career.', cta_text: 'Register Now' },
                                { title: 'Your Next Career Move Starts Here', subtitle: 'Free live session', description: 'Get practical, actionable insights you can use immediately.', cta_text: 'Save My Spot' },
                            ],
                            sale: [
                                { title: 'Limited-Time Offer', subtitle: 'Save on your enrollment today', description: 'Lock in this special pricing before it ends.', cta_text: 'Claim Offer' },
                                { title: 'Early Bird Pricing Ends Soon', subtitle: 'Enroll now and save', description: 'Get the best rate available on this program.', cta_text: 'Book Now' },
                                { title: 'Special Discount Available', subtitle: 'For a limited time only', description: 'Take advantage of this offer before it expires.', cta_text: 'Get This Deal' },
                            ],
                            cohort: [
                                { title: 'New Cohort Now Enrolling', subtitle: 'Limited seats per intake', description: 'Join the next cohort and start your training journey.', cta_text: 'Apply Now' },
                                { title: 'Applications Open for Our Next Intake', subtitle: 'Seats are filling fast', description: 'Secure your place in our upcoming cohort today.', cta_text: 'Book Now' },
                                { title: 'Your Cohort Starts Soon', subtitle: 'Don\\'t miss the next intake', description: 'Get hands-on training with a dedicated cohort of peers.', cta_text: 'Enroll Today' },
                            ],
                            free: [
                                { title: 'Try It Free', subtitle: 'No commitment required', description: 'Experience our training approach with zero risk.', cta_text: 'Start Free' },
                                { title: 'Free Access, Real Results', subtitle: 'Get started at no cost', description: 'See why students choose Kazilink Digital Academy.', cta_text: 'Get Started' },
                                { title: 'Explore for Free', subtitle: 'See what you\\'ll learn', description: 'Preview our training before you commit.', cta_text: 'Try Now' },
                            ],
                            generic: [
                                { title: 'Transform Your Career Today', subtitle: 'Practical, job-ready training', description: 'Join thousands of graduates who have upskilled with Kazilink.', cta_text: 'Book Now' },
                                { title: 'Learn In-Demand Skills', subtitle: 'Taught by industry experts', description: 'Hands-on training that gets you real results.', cta_text: 'Enroll Now' },
                                { title: 'Start Your Training Journey', subtitle: 'Flexible online and in-person options', description: 'Build the skills employers are looking for.', cta_text: 'Get Started' },
                            ],
                        };

                        this.variations = templates[bucket].map(v => ({ ...v, applied: false }));
                        this.generating = false;
                    }, 800);
                },
                regenerate(index) {
                    this.generate();
                },
                apply(variation, index) {
                    $wire.set('formData.title', variation.title);
                    $wire.set('formData.subtitle', variation.subtitle);
                    $wire.set('formData.description', variation.description);
                    $wire.set('formData.cta_text', variation.cta_text);
                    this.variations = this.variations.map((v, i) => ({ ...v, applied: i === index }));
                },
            }"
            class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
            wire:click.self="$set('showForm', false)"
        >
            <div class="bg-background border border-border rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto shadow-2xl">
                <div class="sticky top-0 bg-background flex items-center justify-between p-5 border-b border-border z-10">
                    <h2 class="font-bold text-lg">{{ $editingId ? 'Edit Campaign' : 'New Campaign' }}</h2>
                    <button wire:click="$set('showForm', false)" class="p-2 hover:bg-muted rounded-lg">&times;</button>
                </div>
                <div class="p-5 space-y-5">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium mb-1 block">Campaign Name *</label>
                            <input wire:model="formData.campaign_name" placeholder="e.g. August Intake Early Bird" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @error('formData.campaign_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Type</label>
                            <select wire:model="formData.type" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                @foreach ($types as $type)
                                    <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Status</label>
                            <select wire:model="formData.status" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="draft">Draft</option>
                                <option value="active">Active</option>
                                <option value="paused">Paused</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-brand-50 border border-brand-200 rounded-xl p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-sm flex items-center gap-1.5"><x-core::icon name="sparkles" class="w-4 h-4 text-brand-500" /> AI Ad Copy Generator</h3>
                            <button
                                type="button"
                                @click="generate()"
                                :disabled="! $wire.formData.campaign_name || generating"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold bg-brand-500 hover:bg-brand-600 text-white px-3 py-1.5 rounded-lg disabled:opacity-50 transition-colors"
                            >
                                <x-core::icon name="sparkles" class="w-3.5 h-3.5" x-show="!generating" />
                                <x-core::icon name="loader" class="w-3.5 h-3.5 animate-spin" x-show="generating" x-cloak />
                                <span x-text="generating ? 'Generating...' : 'Generate Copy'"></span>
                            </button>
                        </div>
                        <template x-if="variations.length">
                            <div class="grid sm:grid-cols-3 gap-2">
                                <template x-for="(variation, index) in variations" :key="index">
                                    <div class="bg-background border border-border rounded-xl p-3 space-y-1.5">
                                        <p class="font-semibold text-xs" x-text="variation.title"></p>
                                        <p class="text-xs text-muted-foreground" x-text="variation.subtitle"></p>
                                        <div class="flex items-center justify-between pt-1">
                                            <button type="button" @click="apply(variation, index)" class="text-xs font-medium text-brand-600 hover:underline inline-flex items-center gap-1">
                                                <x-core::icon name="copy" class="w-3 h-3" />
                                                <span x-text="variation.applied ? 'Applied' : 'Use this'"></span>
                                            </button>
                                            <button type="button" @click="regenerate(index)" class="text-muted-foreground hover:text-foreground">
                                                <x-core::icon name="refresh-cw" class="w-3 h-3" />
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium mb-1 block">Title</label>
                            <input wire:model="formData.title" placeholder="Ad headline" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Subtitle</label>
                            <input wire:model="formData.subtitle" placeholder="Eye-catching subtitle" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">CTA Text</label>
                            <input wire:model="formData.cta_text" placeholder="e.g. Book Now" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">CTA Link</label>
                            <input wire:model="formData.cta_link" placeholder="/booking or /programs/..." class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium mb-1 block">Description</label>
                            <textarea wire:model="formData.description" rows="3" placeholder="Ad description / body copy" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <livewire:core::image-upload wire:model="formData.desktop_image_url" folder="advertisements" label="Image (optional — used as background)" :key="'ad-image-'.($editingId ?? 'new')" />
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Priority (higher = shown first)</label>
                            <input type="number" wire:model="formData.priority" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Background Color</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model="formData.background_color" class="w-10 h-10 rounded-lg border border-border cursor-pointer">
                                <input type="text" wire:model="formData.background_color" class="flex-1 px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Publish Date</label>
                            <input type="datetime-local" wire:model="formData.publish_date" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Expiry Date</label>
                            <input type="datetime-local" wire:model="formData.expiry_date" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-medium mb-1.5 block">Placement</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($placements as $placement)
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="checkbox" wire:model="formData.placement" value="{{ $placement }}" class="w-4 h-4 rounded">
                                    {{ ucfirst($placement) }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    @if ($formData['title'])
                        <div>
                            <label class="text-xs font-medium mb-1.5 block">Live Preview</label>
                            <div class="relative rounded-xl overflow-hidden h-40 bg-cover bg-center flex items-end p-4"
                                 style="background-color: {{ $formData['background_color'] ?: '#0c4a6e' }}; @if($formData['desktop_image_url']) background-image: url('{{ $formData['desktop_image_url'] }}'); @endif">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                <div class="relative text-white">
                                    @if ($formData['subtitle'])
                                        <p class="text-xs font-medium opacity-90">{{ $formData['subtitle'] }}</p>
                                    @endif
                                    <p class="font-black text-lg">{{ $formData['title'] }}</p>
                                    @if ($formData['description'])
                                        <p class="text-xs opacity-90 line-clamp-1">{{ $formData['description'] }}</p>
                                    @endif
                                    @if ($formData['cta_text'])
                                        <span class="inline-block mt-2 bg-white text-gray-900 text-xs font-semibold px-3 py-1.5 rounded-lg">{{ $formData['cta_text'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-2 border-t border-border">
                        <button wire:click="$set('showForm', false)" class="border border-border rounded-xl px-4 py-2 text-sm font-medium hover:bg-muted transition-colors">Cancel</button>
                        <button wire:click="save" class="bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors">
                            {{ $editingId ? 'Update Campaign' : 'Create Campaign' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
