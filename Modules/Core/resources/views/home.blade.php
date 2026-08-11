<x-core::layouts.public>
    @php
        $iconMap = [
            'Users' => 'users', 'BookOpen' => 'book-open', 'TrendingUp' => 'trending-up',
            'Globe' => 'globe', 'Award' => 'award', 'DollarSign' => 'dollar-sign', 'Zap' => 'zap',
        ];
        $defaultHeroImage = 'images/hero-image.jpeg';
        $heroImageUrl = ! empty($settings['hero_image_url'])
            ? $settings['hero_image_url']
            : (file_exists(public_path($defaultHeroImage)) ? asset($defaultHeroImage) : null);
    @endphp

    <!-- HERO -->
    <section class="relative min-h-[90vh] flex items-center overflow-hidden bg-gray-950">
        @if ($heroImageUrl)
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image:url('{{ $heroImageUrl }}')"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-gray-950 via-gray-950/75 to-gray-950/25"></div>
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-gray-950 via-navy-950 to-gray-950"></div>
        @endif

        <div class="absolute top-1/4 right-1/4 w-72 h-72 bg-brand-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/3 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="max-w-3xl xl:max-w-4xl 2xl:max-w-5xl">
                <div class="inline-flex items-center gap-2 bg-brand-500/10 border border-brand-500/20 text-brand-400 text-sm font-medium px-4 py-2 rounded-full mb-6">
                    <x-core::icon name="zap" class="w-3.5 h-3.5" />
                    Kenya's #1 Online Income Academy
                </div>

                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black text-white mb-6 leading-[1.05]">
                    {{ $settings['hero_title'] ?? "Learn. Earn.\nThrive Online." }}
                </h1>

                <p class="text-lg sm:text-xl text-gray-300 mb-8 leading-relaxed max-w-2xl xl:max-w-3xl">
                    {{ $settings['hero_subtitle'] ?? 'Master high-income digital skills through hands-on training. Join thousands of Kenyans earning from anywhere.' }}
                </p>

                <div class="flex flex-col sm:flex-row gap-3 mb-10">
                    <a href="{{ url('/register') }}" class="inline-flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold text-base px-7 py-4 rounded-xl transition-all duration-200 shadow-2xl shadow-brand-500/40 hover:shadow-brand-500/60 hover:scale-[1.02] active:scale-[0.98]">
                        {{ $settings['hero_cta_primary'] ?? 'Enroll Now' }}
                        <x-core::icon name="arrow-right" class="w-4 h-4" />
                    </a>
                    <a href="{{ url('/programs') }}" class="inline-flex items-center justify-center gap-2 border-2 border-white/20 text-white hover:border-white/40 hover:bg-white/5 font-semibold text-base px-7 py-4 rounded-xl transition-all duration-200">
                        <x-core::icon name="play" class="w-4 h-4" />
                        {{ $settings['hero_cta_secondary'] ?? 'Explore Courses' }}
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-6 text-sm text-gray-400">
                    <div class="flex items-center gap-2">
                        <div class="flex -space-x-2">
                            @for ($i = 0; $i < 4; $i++)
                                <img src="{{ asset('favicon.jpeg') }}" alt="" class="w-7 h-7 rounded-full border-2 border-gray-950 object-cover">
                            @endfor
                        </div>
                        <span>{{ $statsByKey['students_trained']['value'] ?? '2,000+' }} students trained</span>
                    </div>
                    <div class="flex items-center gap-1 text-amber-400">
                        @for ($i = 0; $i < 5; $i++)
                            <x-core::icon name="star" class="w-3.5 h-3.5 fill-current" />
                        @endfor
                        <span class="text-gray-400 ml-1">{{ $statsByKey['avg_rating']['value'] ?? '4.8/5' }} rating</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 80" fill="none" class="w-full h-16 sm:h-20"><path d="M0 80H1440V20C1200 80 960 0 720 40C480 80 240 0 0 20V80Z" class="fill-background"/></svg>
        </div>
    </section>

    <!-- TRUST INDICATORS -->
    <section class="py-8 bg-background border-b border-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-6">
                @foreach ([
                    ['icon' => 'user-check', 'label' => 'Expert-Led Training'],
                    ['icon' => 'file-text', 'label' => 'Digital Certificates'],
                    ['icon' => 'trending-up', 'label' => 'Career-Focused Courses'],
                    ['icon' => 'globe', 'label' => 'Learn Anytime, Anywhere'],
                    ['icon' => 'dollar-sign', 'label' => 'Earn From Home'],
                ] as $indicator)
                    <div class="flex flex-col items-center text-center gap-2">
                        <x-core::icon :name="$indicator['icon']" class="w-6 h-6 text-brand-500" />
                        <span class="text-xs sm:text-sm font-medium text-foreground">{{ $indicator['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- PROMOTIONAL AD BANNER -->
    @if (count($ads) > 0)
        <section
            class="relative overflow-hidden bg-gray-950 border-y border-brand-500/20"
            x-data="{ ads: @js($ads), active: 0, timer: null }"
            x-init="if (ads.length > 1) { timer = setInterval(() => { active = (active + 1) % ads.length }, 6000) }"
        >
            <template x-for="(ad, i) in ads" :key="ad.id">
                <div x-show="active === i" x-cloak>
                    <div class="absolute inset-0 bg-cover bg-center opacity-30" :style="ad.desktop_image_url ? `background-image:url(${ad.desktop_image_url})` : ''"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-950 via-gray-950/80 to-gray-950/50"></div>
                    <div class="absolute top-0 left-0 w-96 h-96 bg-brand-500/10 rounded-full blur-3xl animate-pulse"></div>
                    <div class="absolute bottom-0 right-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl"></div>

                    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
                        <div class="flex flex-col lg:flex-row items-center gap-6">
                            <div class="flex-1 min-w-0 text-center lg:text-left">
                                <div x-show="ad.subtitle" class="inline-flex items-center gap-1.5 bg-brand-500/15 border border-brand-500/30 text-brand-400 text-xs font-bold px-3 py-1 rounded-full mb-3 uppercase tracking-wider">
                                    <x-core::icon name="zap" class="w-3 h-3" />
                                    <span x-text="ad.subtitle"></span>
                                </div>
                                <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-white mb-2 leading-tight" x-text="ad.title"></h2>
                                <p x-show="ad.description" class="text-sm sm:text-base text-gray-300 max-w-2xl mx-auto lg:mx-0" x-text="ad.description"></p>
                            </div>

                            <div x-show="ad.cta_text && ad.cta_link" class="flex-shrink-0 flex flex-col items-center gap-2">
                                <a :href="ad.cta_link" class="group inline-flex items-center gap-2 bg-gradient-to-r from-brand-500 to-navy-600 hover:from-brand-400 hover:to-blue-500 text-white font-bold text-base px-7 py-4 rounded-xl transition-all duration-200 shadow-2xl shadow-brand-500/40 hover:shadow-brand-500/60 hover:scale-105 active:scale-95">
                                    <span x-text="ad.cta_text"></span>
                                    <x-core::icon name="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                                </a>
                                <div x-show="ads.length > 1" class="flex gap-1.5 mt-1">
                                    <template x-for="(a, j) in ads" :key="j">
                                        <button @click="active = j" :class="active === j ? 'bg-brand-400 w-6' : 'bg-white/30 w-1.5 hover:bg-white/50'" class="h-1.5 rounded-full transition-all"></button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </section>
    @endif

    <!-- STATS -->
    @if (count($stats) > 0)
        <section class="py-12 bg-background">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                    @foreach ($stats as $stat)
                        <div class="text-center group">
                            <div class="flex justify-center mb-2 text-brand-500 group-hover:scale-110 transition-transform">
                                <x-core::icon :name="$iconMap[$stat['icon']] ?? 'trending-up'" class="w-7 h-7" />
                            </div>
                            <div class="text-2xl sm:text-3xl font-black text-foreground mb-0.5">{{ $stat['value'] }}</div>
                            <div class="text-xs text-muted-foreground font-medium">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- CEO MESSAGE -->
    @if ($founder)
        @php
            $bioParagraphs = array_values(array_filter(preg_split('/\n\s*\n/', trim($founder['bio'] ?? '')), fn ($p) => trim($p) !== ''));
            $bioClosing = count($bioParagraphs) > 1 ? array_pop($bioParagraphs) : null;
        @endphp
        <section class="relative py-20 sm:py-24 overflow-hidden bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900">
            <div class="absolute top-0 left-1/4 w-72 h-72 bg-brand-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl"></div>

            <div class="relative max-w-6xl mx-auto container-padding">
                <span class="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-6 block text-center">Welcome from the Founder</span>

                <div class="flow-root">
                    <figure class="float-none sm:float-left sm:mr-8 lg:mr-10 mb-6 w-full sm:w-72 lg:w-96">
                        <div class="relative">
                            <div class="absolute -inset-2 rounded-2xl bg-gradient-to-br from-brand-500/40 to-blue-500/20 blur-xl"></div>
                            @if (! empty($founder['avatar_url']))
                                <img src="{{ $founder['avatar_url'] }}" alt="{{ $founder['full_name'] }}" loading="lazy" class="relative w-full aspect-[4/5] object-cover rounded-2xl ring-4 ring-white/10 shadow-2xl">
                            @else
                                <div class="relative w-full aspect-[4/5] rounded-2xl bg-gradient-to-br from-brand-500 to-navy-600 flex items-center justify-center text-white text-6xl font-black ring-4 ring-white/10 shadow-2xl">
                                    {{ strtoupper(substr($founder['full_name'], 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <figcaption class="mt-4 text-center sm:text-left">
                            <p class="font-bold text-white text-lg">{{ $founder['full_name'] }}</p>
                            <p class="text-sm text-brand-400 font-medium">{{ $founder['title'] }}, KAZI Link Academy</p>
                        </figcaption>
                    </figure>

                    <x-core::icon name="quote" class="hidden sm:inline-block w-8 h-8 text-brand-500/25 float-left mr-2 -mt-1" />
                    <div class="text-gray-300 leading-relaxed text-base sm:text-lg space-y-4">
                        @foreach ($bioParagraphs as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>

                    @if ($bioClosing)
                        <p class="clear-both text-white font-semibold text-lg sm:text-xl mt-8 pt-6 border-t border-white/10">{{ $bioClosing }}</p>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <!-- WHY CHOOSE US -->
    <section class="section-padding bg-background">
        <div class="max-w-7xl mx-auto container-padding">
            <div class="text-center mb-10">
                <h2 class="text-3xl sm:text-4xl font-black text-foreground">Why Thousands of Learners Choose KAZI Link Academy</h2>
                <p class="text-muted-foreground mt-3 max-w-2xl mx-auto">We don't just teach skills—we prepare you for real opportunities including employment, freelancing, entrepreneurship and career growth.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ([
                    ['icon' => 'user-check', 'title' => 'Expert-Led Training', 'body' => 'Learn directly from experienced professionals teaching practical skills.'],
                    ['icon' => 'trending-up', 'title' => 'Career-Focused Learning', 'body' => 'Every course prepares learners for employment and self-employment.'],
                    ['icon' => 'award', 'title' => 'Recognized Certificates', 'body' => 'Receive professional certificates after successful completion.'],
                    ['icon' => 'globe', 'title' => 'Learn Anywhere', 'body' => 'Study anytime using your phone, tablet or computer.'],
                    ['icon' => 'users', 'title' => 'Lifetime Community', 'body' => 'Remain connected with fellow learners and mentors.'],
                    ['icon' => 'layers', 'title' => 'Practical Projects', 'body' => 'Complete real-world projects that prepare you for actual work.'],
                ] as $card)
                    <div class="bg-card border border-border rounded-2xl p-6 card-hover">
                        <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center mb-4">
                            <x-core::icon :name="$card['icon']" class="w-5 h-5 text-brand-600" />
                        </div>
                        <h3 class="font-bold text-lg mb-2">{{ $card['title'] }}</h3>
                        <p class="text-sm text-muted-foreground">{{ $card['body'] }}</p>
                    </div>
                @endforeach
            </div>

            <p class="text-center text-lg font-semibold text-foreground mt-10 max-w-xl mx-auto">
                Education should not end with knowledge.<br>It should lead to opportunity.
            </p>
        </div>
    </section>

    <!-- FEATURED PROGRAMS -->
    <section class="section-padding bg-muted/30">
        <div class="max-w-7xl mx-auto container-padding">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
                <div>
                    <span class="text-brand-600 font-semibold text-sm uppercase tracking-wider mb-2 block">Our Programs</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-foreground">Learn Skills That Pay</h2>
                    <p class="text-muted-foreground mt-2">Practical, results-driven training built for the real world.</p>
                </div>
                <a href="{{ url('/programs') }}" class="flex items-center gap-1.5 text-brand-600 font-semibold hover:gap-2.5 transition-all whitespace-nowrap">
                    View All Programs <x-core::icon name="arrow-right" class="w-4 h-4" />
                </a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($programs as $program)
                    <x-academy::program-card :program="$program" />
                @empty
                    <div class="col-span-3 text-center py-12 text-muted-foreground">No programs found.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- UPCOMING COHORTS -->
    @if (count($cohorts) > 0)
        <section class="section-padding bg-background">
            <div class="max-w-7xl mx-auto container-padding">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
                    <div>
                        <span class="text-brand-600 font-semibold text-sm uppercase tracking-wider mb-2 block">Upcoming Cohorts</span>
                        <h2 class="text-3xl sm:text-4xl font-black text-foreground">Next Intakes</h2>
                        <p class="text-muted-foreground mt-2">Seats are limited. Book yours before they fill up.</p>
                    </div>
                    <a href="{{ url('/cohorts') }}" class="flex items-center gap-1.5 text-brand-600 font-semibold hover:gap-2.5 transition-all whitespace-nowrap">
                        View All Cohorts <x-core::icon name="arrow-right" class="w-4 h-4" />
                    </a>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach ($cohorts as $cohort)
                        <x-academy::cohort-card :cohort="$cohort" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- TRAINERS -->
    @if (count($trainers) > 0)
        <section class="section-padding bg-muted/30">
            <div class="max-w-7xl mx-auto container-padding">
                <div class="text-center mb-10">
                    <span class="text-brand-600 font-semibold text-sm uppercase tracking-wider mb-2 block">Expert Trainers</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-foreground">Learn From Real Practitioners</h2>
                    <p class="text-muted-foreground mt-2 max-w-xl mx-auto">Our trainers don't just teach — they actively earn online using the exact skills they teach you.</p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($trainers as $trainer)
                        <div class="bg-card border border-border rounded-2xl p-5 text-center card-hover group">
                            <div class="relative inline-block mb-4">
                                @if ($trainer['avatar_url'])
                                    <img src="{{ $trainer['avatar_url'] }}" alt="{{ $trainer['full_name'] }}" loading="lazy" class="w-20 h-20 rounded-full object-cover mx-auto group-hover:ring-4 ring-brand-500/30 transition-all">
                                @else
                                    <div class="w-20 h-20 rounded-full bg-brand-500 flex items-center justify-center text-white text-2xl font-bold mx-auto">
                                        {{ strtoupper(substr($trainer['full_name'], 0, 1)) }}
                                    </div>
                                @endif
                                <div class="absolute bottom-0 right-0 w-6 h-6 bg-green-500 rounded-full border-2 border-card flex items-center justify-center">
                                    <x-core::icon name="check-circle" class="w-3 h-3 text-white" />
                                </div>
                            </div>
                            <h3 class="font-bold text-foreground mb-1">{{ $trainer['full_name'] }}</h3>
                            @if ($trainer['title'])
                                <p class="text-xs text-brand-600 font-medium mb-2">{{ $trainer['title'] }}</p>
                            @endif
                            @if ($trainer['rating'] > 0)
                                <div class="flex items-center justify-center gap-1 text-amber-500 text-xs">
                                    <x-core::icon name="star" class="w-3.5 h-3.5 fill-current" />
                                    {{ number_format($trainer['rating'], 1) }} ({{ $trainer['review_count'] }} reviews)
                                </div>
                            @endif
                            @if (! empty($trainer['specializations']))
                                <div class="flex flex-wrap gap-1 justify-center mt-3">
                                    @foreach (array_slice($trainer['specializations'], 0, 3) as $s)
                                        <span class="text-[10px] bg-brand-50 text-brand-700 px-2 py-0.5 rounded-full font-medium">{{ $s }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-8">
                    <a href="{{ url('/about#trainers') }}" class="inline-flex items-center gap-2 text-brand-600 font-semibold hover:gap-3 transition-all">
                        Meet All Trainers <x-core::icon name="arrow-right" class="w-4 h-4" />
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- TESTIMONIALS -->
    @if (count($testimonials) > 0)
        <section class="section-padding bg-background">
            <div class="max-w-7xl mx-auto container-padding">
                <div class="text-center mb-10">
                    <span class="text-brand-600 font-semibold text-sm uppercase tracking-wider mb-2 block">Student Success</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-foreground">Real Results. Real People.</h2>
                    <p class="text-muted-foreground mt-2 max-w-xl mx-auto">Stories from graduates who transformed their income with Kazilink training.</p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach (array_slice($testimonials, 0, 3) as $testimonial)
                        <x-cms::testimonial-card :testimonial="$testimonial" />
                    @endforeach
                </div>

                @if (count($testimonials) > 3)
                    <div class="text-center mt-8">
                        <a href="{{ url('/success-stories') }}" class="inline-flex items-center gap-2 text-brand-600 font-semibold hover:gap-3 transition-all">
                            Read More Success Stories <x-core::icon name="arrow-right" class="w-4 h-4" />
                        </a>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <!-- CTA BANNER -->
    @if ($cta)
        <section class="py-20 relative" style="background-color: {{ $cta['background_color'] ?? '#0f172a' }}">
            @if (! empty($cta['background_image_url']))
                <div class="absolute inset-0 bg-cover bg-center opacity-20" style="background-image:url('{{ $cta['background_image_url'] }}')"></div>
            @endif
            <div class="relative max-w-4xl mx-auto text-center container-padding">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-white mb-4">{{ $cta['title'] }}</h2>
                @if (! empty($cta['subtitle']))
                    <p class="text-lg text-white/80 mb-3">{{ $cta['subtitle'] }}</p>
                @endif
                @if (! empty($cta['description']))
                    <p class="text-white/60 mb-8 max-w-2xl mx-auto">{{ $cta['description'] }}</p>
                @endif
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    @if (! empty($cta['button_one_text']) && ! empty($cta['button_one_link']))
                        <a href="{{ $cta['button_one_link'] }}" class="inline-flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold px-7 py-4 rounded-xl transition-all hover:scale-[1.02] shadow-2xl shadow-brand-500/40">
                            {{ $cta['button_one_text'] }} <x-core::icon name="arrow-right" class="w-4 h-4" />
                        </a>
                    @endif
                    @if (! empty($cta['button_two_text']) && ! empty($cta['button_two_link']))
                        <a href="{{ $cta['button_two_link'] }}" class="inline-flex items-center justify-center gap-2 border-2 border-white/30 text-white hover:border-white/60 font-semibold px-7 py-4 rounded-xl transition-all">
                            {{ $cta['button_two_text'] }}
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @endif
</x-core::layouts.public>
