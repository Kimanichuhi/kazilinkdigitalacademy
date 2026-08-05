<div>
    <div class="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Get In Touch</span>
            <h1 class="text-4xl sm:text-5xl font-black text-white mb-4">Contact Us</h1>
            <p class="text-gray-400 text-lg">We'd love to hear from you. Reach out with any questions.</p>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid lg:grid-cols-5 gap-8">
        <div class="lg:col-span-2 space-y-4">
            @if (!empty($settings['contact_email']))
                <div class="bg-card border border-border rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center flex-shrink-0">
                        <x-core::icon name="mail" class="w-4 h-4 text-brand-600" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-muted-foreground">Email</p>
                        <a href="mailto:{{ $settings['contact_email'] }}" class="font-semibold text-sm truncate block">{{ $settings['contact_email'] }}</a>
                    </div>
                </div>
            @endif
            @if (!empty($settings['contact_phone']))
                <div class="bg-card border border-border rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center flex-shrink-0">
                        <x-core::icon name="phone" class="w-4 h-4 text-brand-600" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-muted-foreground">Phone</p>
                        <a href="tel:{{ $settings['contact_phone'] }}" class="font-semibold text-sm truncate block">{{ $settings['contact_phone'] }}</a>
                    </div>
                </div>
            @endif
            @if (!empty($settings['contact_address']))
                <div class="bg-card border border-border rounded-2xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center flex-shrink-0">
                        <x-core::icon name="map-pin" class="w-4 h-4 text-brand-600" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-muted-foreground">Address</p>
                        <p class="font-semibold text-sm">{{ $settings['contact_address'] }}</p>
                    </div>
                </div>
            @endif
            <div class="bg-card border border-border rounded-2xl p-4">
                <p class="text-xs text-muted-foreground mb-1">Office Hours</p>
                <p class="text-sm font-semibold">Mon – Fri: 8:00 AM – 6:00 PM</p>
                <p class="text-sm font-semibold">Sat: 9:00 AM – 2:00 PM</p>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="bg-card border border-border rounded-2xl p-6">
                @if ($submitted)
                    <div class="text-center py-10">
                        <x-core::icon name="check-circle" class="w-12 h-12 text-green-500 mx-auto mb-3" />
                        <h3 class="font-bold text-lg mb-1">Message Sent!</h3>
                        <p class="text-sm text-muted-foreground">We'll get back to you within 24 hours.</p>
                        <button wire:click="$set('submitted', false)" class="mt-4 text-sm font-medium text-brand-600 hover:text-brand-700">Send another message</button>
                    </div>
                @else
                    <form wire:submit="submit" class="space-y-4">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium mb-1 block">Full Name *</label>
                                <input wire:model="full_name" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-xs font-medium mb-1 block">Email *</label>
                                <input type="email" wire:model="email" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium mb-1 block">Phone</label>
                                <input wire:model="phone" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="text-xs font-medium mb-1 block">Subject *</label>
                                <select wire:model="subject" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    <option value="enrollment">Enrollment</option>
                                    <option value="payment">Payment</option>
                                    <option value="technical">Technical</option>
                                    <option value="corporate">Corporate</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium mb-1 block">Message *</label>
                            <textarea wire:model="message" rows="5" class="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"></textarea>
                            @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="w-full bg-brand-500 hover:bg-brand-600 text-white rounded-xl px-4 py-2.5 text-sm font-semibold transition-colors">
                            Send Message
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
