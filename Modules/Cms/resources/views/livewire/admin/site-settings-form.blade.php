<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black">Settings</h1>
            <p class="text-sm text-muted-foreground">Site-wide configuration</p>
        </div>
        <button wire:click="save" wire:loading.attr="disabled" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2 text-sm font-semibold transition-colors disabled:opacity-50">
            <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-2"><x-core::icon name="check-circle" class="w-4 h-4" /> Save Changes</span>
            <span wire:loading wire:target="save" class="inline-flex items-center gap-2"><x-core::icon name="refresh-cw" class="w-4 h-4 animate-spin" /> Saving...</span>
        </button>
    </div>

    @if ($savedMessage)
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">{{ $savedMessage }}</div>
    @endif

    <div class="grid lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1 space-y-0.5">
            @foreach ($groups as $group)
                <button
                    wire:click="$set('activeGroup', '{{ $group['key'] }}')"
                    class="w-full text-left px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ $activeGroup === $group['key'] ? 'bg-brand-500/15 text-brand-600' : 'text-muted-foreground hover:bg-muted' }}"
                >
                    {{ $group['label'] }}
                </button>
            @endforeach
        </div>

        <div class="lg:col-span-3 bg-card border border-border rounded-2xl p-6 space-y-5">
            @foreach ($groups as $group)
                @if ($activeGroup === $group['key'])
                    @foreach ($group['fields'] as $field)
                        <div>
                            @if ($field['type'] !== 'image')
                                <label class="text-sm font-medium mb-1.5 block">{{ $field['label'] }}</label>
                            @endif

                            @if ($field['type'] === 'textarea')
                                <textarea wire:model="values.{{ $field['key'] }}" rows="3" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                            @elseif ($field['type'] === 'color')
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model="values.{{ $field['key'] }}" class="w-10 h-10 rounded-lg border border-border cursor-pointer">
                                    <input type="text" wire:model="values.{{ $field['key'] }}" class="flex-1 px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>
                            @elseif ($field['type'] === 'select')
                                <select wire:model="values.{{ $field['key'] }}" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    <option value="false">Disabled</option>
                                    <option value="true">Enabled</option>
                                </select>
                            @elseif ($field['type'] === 'image')
                                <livewire:core::image-upload wire:model="values.{{ $field['key'] }}" folder="settings" :label="$field['label']" :key="'setting-'.$field['key']" />
                            @else
                                <input type="{{ $field['type'] }}" wire:model="values.{{ $field['key'] }}" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @endif

                            @if (! empty($field['description']) && $field['type'] !== 'image')
                                <p class="text-xs text-muted-foreground mt-1">{{ $field['description'] }}</p>
                            @endif
                        </div>
                    @endforeach
                @endif
            @endforeach
        </div>
    </div>
</div>
