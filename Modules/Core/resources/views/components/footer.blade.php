@php
    $quickLinks = [
        ['label' => 'Programs', 'url' => '/programs'],
        ['label' => 'Upcoming Cohorts', 'url' => '/cohorts'],
        ['label' => 'Pricing', 'url' => '/pricing'],
        ['label' => 'About Us', 'url' => '/about'],
        ['label' => 'Blog', 'url' => '/blog'],
        ['label' => 'Resources', 'url' => '/resources'],
        ['label' => 'FAQ', 'url' => '/faq'],
        ['label' => 'Contact', 'url' => '/contact'],
    ];
    $programLinks = [
        ['label' => 'Freelancing Mastery', 'url' => '/programs/freelancing-mastery-bootcamp'],
        ['label' => 'E-Commerce Empire', 'url' => '/programs/ecommerce-empire-builder'],
        ['label' => 'Digital Marketing', 'url' => '/programs/digital-marketing-professional'],
        ['label' => 'Full-Stack Development', 'url' => '/programs/full-stack-web-development'],
        ['label' => 'Content Creation', 'url' => '/programs/content-creation-monetization'],
        ['label' => 'Forex & Crypto Trading', 'url' => '/programs/forex-crypto-trading-mastery'],
    ];
    $socialIcons = [
        'social_facebook' => 'facebook',
        'social_twitter' => 'twitter',
        'social_instagram' => 'instagram',
        'social_linkedin' => 'linkedin',
        'social_youtube' => 'youtube',
    ];
    $socials = collect($socialIcons)->filter(fn ($platform, $key) => ! empty($settings[$key]));
@endphp

<footer class="bg-navy-950 text-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            <!-- Brand -->
            <div class="lg:col-span-1">
                <a href="{{ url('/') }}" class="flex items-center mb-4 group">
                    <div class="bg-white rounded-lg px-2 py-1.5">
                        <img src="{{ asset('kazilink-logo.png') }}" alt="{{ $settings['site_name'] ?? 'Kazilink Digital Academy' }}" loading="lazy" class="h-10 w-auto">
                    </div>
                </a>
                <p class="text-sm text-gray-400 leading-relaxed mb-5">
                    {{ $settings['footer_tagline'] ?? 'Empowering Africans to earn online through practical digital skills training.' }}
                </p>
                <div class="space-y-2 text-sm">
                    @if (! empty($settings['contact_email']))
                        <a href="mailto:{{ $settings['contact_email'] }}" class="flex items-center gap-2 text-gray-400 hover:text-brand-400 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 5L2 7"/></svg>
                            {{ $settings['contact_email'] }}
                        </a>
                    @endif
                    @if (! empty($settings['contact_phone']))
                        <a href="tel:{{ $settings['contact_phone'] }}" class="flex items-center gap-2 text-gray-400 hover:text-brand-400 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            {{ $settings['contact_phone'] }}
                        </a>
                    @endif
                    @if (! empty($settings['contact_address']))
                        <div class="flex items-center gap-2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $settings['contact_address'] }}
                        </div>
                    @endif
                </div>
                @if ($socials->isNotEmpty())
                    <div class="flex gap-3 mt-5">
                        @foreach ($socials as $key => $platform)
                            <a href="{{ $settings[$key] }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-navy-800 hover:bg-brand-600 flex items-center justify-center transition-colors text-gray-400 hover:text-white" aria-label="{{ ucfirst($platform) }}">
                                <span class="text-xs font-bold uppercase">{{ substr($platform, 0, 2) }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    @foreach ($quickLinks as $link)
                        <li><a href="{{ url($link['url']) }}" class="text-sm text-gray-400 hover:text-brand-400 transition-colors">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Programs -->
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Programs</h3>
                <ul class="space-y-2">
                    @foreach ($programLinks as $link)
                        <li><a href="{{ url($link['url']) }}" class="text-sm text-gray-400 hover:text-brand-400 transition-colors">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Newsletter -->
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Stay Updated</h3>
                <p class="text-sm text-gray-400 mb-4">Get free income tips, success stories, and new cohort alerts.</p>
                <form class="space-y-2" onsubmit="return false;">
                    <input type="email" placeholder="your@email.com" class="w-full px-3 py-2.5 text-sm bg-navy-800 border border-navy-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-brand-500 transition-colors">
                    <button type="submit" class="w-full bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">Subscribe Free</button>
                </form>
                <p class="text-xs text-gray-500 mt-2">No spam. Unsubscribe anytime.</p>
            </div>
        </div>

        <div class="border-t border-navy-800 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-xs text-gray-500">
                &copy; {{ date('Y') }} {{ $settings['site_name'] ?? 'Kazilink Digital Academy' }}. All rights reserved.
            </p>
            <div class="flex gap-4 text-xs text-gray-500">
                <a href="{{ url('/privacy') }}" class="hover:text-brand-400 transition-colors">Privacy Policy</a>
                <a href="{{ url('/terms') }}" class="hover:text-brand-400 transition-colors">Terms of Service</a>
                <a href="{{ url('/refund') }}" class="hover:text-brand-400 transition-colors">Refund Policy</a>
            </div>
        </div>
    </div>
</footer>
