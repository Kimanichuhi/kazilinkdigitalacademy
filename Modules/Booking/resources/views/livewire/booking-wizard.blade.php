@php
    $steps = [
        1 => ['label' => 'Program', 'icon' => 'book-open'],
        2 => ['label' => 'Details', 'icon' => 'user'],
        3 => ['label' => 'Payment', 'icon' => 'dollar-sign'],
        4 => ['label' => 'Review', 'icon' => 'shield'],
    ];
    $amount = $selectedCohort['price'] ?? $selectedProgram['price'] ?? null;
    $currency = $selectedProgram['currency'] ?? 'KES';
@endphp
<div>
    @if ($step === 5 && $bookingNumber)
        <div class="min-h-[60vh] flex items-center justify-center p-8">
            <div class="max-w-lg w-full text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <x-core::icon name="check-circle" class="w-10 h-10 text-green-600" />
                </div>
                <h1 class="text-3xl font-black text-foreground mb-3">Booking Submitted!</h1>
                <p class="text-muted-foreground mb-2">Your booking reference is:</p>
                <div class="bg-brand-50 border border-brand-200 rounded-xl px-6 py-4 mb-6">
                    <p class="text-2xl font-mono font-bold text-brand-600">{{ $bookingNumber }}</p>
                </div>
                <p class="text-sm text-muted-foreground mb-8">
                    We've received your booking. Our team will review your application and contact you within 24 hours with payment confirmation and next steps.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ url('/student') }}" class="btn-primary inline-flex items-center gap-2 justify-center">
                        Go to My Dashboard <x-core::icon name="arrow-right" class="w-4 h-4" />
                    </a>
                    <a href="{{ url('/programs') }}" class="btn-outline inline-flex items-center gap-2 justify-center">
                        Browse More Programs
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Header -->
        <div class="bg-gradient-to-r from-gray-950 to-blue-950 py-10">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
                <h1 class="text-3xl font-black text-white mb-2">Book Your Training</h1>
                <p class="text-gray-400">Complete the form below to secure your spot</p>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Stepper -->
            <div class="flex items-center justify-center gap-2 mb-10">
                @foreach ($steps as $n => $s)
                    <div class="flex items-center">
                        <div class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ $step === $n ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/30' : ($step > $n ? 'bg-green-500 text-white' : 'bg-muted text-muted-foreground') }}">
                            <x-core::icon :name="$step > $n ? 'check-circle' : $s['icon']" class="w-4 h-4" />
                            <span class="hidden sm:inline">{{ $s['label'] }}</span>
                        </div>
                        @if ($n < 4)
                            <div class="w-8 h-0.5 mx-1 {{ $step > $n ? 'bg-green-500' : 'bg-border' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Step 1: Program Selection -->
            @if ($step === 1)
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold">Choose Your Program</h2>

                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach ($programs as $p)
                            <button
                                wire:click="selectProgram('{{ $p['id'] }}')"
                                type="button"
                                class="text-left border-2 rounded-2xl p-4 transition-all hover:border-brand-500 {{ ($selectedProgram['id'] ?? null) === $p['id'] ? 'border-brand-500 bg-brand-50' : 'border-border' }}"
                            >
                                <div class="flex gap-3">
                                    @if ($p['thumbnail_url'])
                                        <img src="{{ $p['thumbnail_url'] }}" alt="{{ $p['title'] }}" loading="lazy" class="w-16 h-12 rounded-lg object-cover flex-shrink-0">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-sm line-clamp-2">{{ $p['title'] }}</h3>
                                        <p class="text-xs text-muted-foreground mt-0.5">{{ $p['duration_label'] }}</p>
                                        <p class="text-sm font-bold text-brand-600 mt-1">{{ $p['currency'] }} {{ $p['price'] ? number_format($p['price']) : '' }}</p>
                                    </div>
                                    @if (($selectedProgram['id'] ?? null) === $p['id'])
                                        <x-core::icon name="check-circle" class="w-5 h-5 text-brand-500 flex-shrink-0" />
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    </div>

                    @if ($selectedProgram)
                        <div>
                            <h3 class="font-bold text-lg mb-4">Choose a Cohort (Optional)</h3>
                            @if (count($cohorts) > 0)
                                <div class="grid sm:grid-cols-2 gap-3">
                                    @foreach ($cohorts as $c)
                                        <button
                                            wire:click="selectCohort('{{ $c['id'] }}')"
                                            type="button"
                                            class="text-left border-2 rounded-xl p-4 transition-all hover:border-brand-500 {{ ($selectedCohort['id'] ?? null) === $c['id'] ? 'border-brand-500 bg-brand-50' : 'border-border' }}"
                                        >
                                            <p class="font-semibold text-sm">{{ $c['name'] }}</p>
                                            <p class="text-xs text-muted-foreground mt-1">{{ \Illuminate\Support\Carbon::parse($c['start_date'])->format('d M Y') }}</p>
                                            <p class="text-xs text-muted-foreground">{{ $c['schedule_details'] ?? '' }}</p>
                                            <div class="flex justify-between items-center mt-2">
                                                <span class="text-xs font-medium {{ $c['seats_left'] <= 5 ? 'text-red-500' : 'text-green-600' }}">{{ $c['seats_left'] }} seats left</span>
                                                @if (($selectedCohort['id'] ?? null) === $c['id'])
                                                    <x-core::icon name="check-circle" class="w-4 h-4 text-brand-500" />
                                                @endif
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted-foreground text-sm">No open cohorts available. Your booking will be placed for the next available cohort.</p>
                            @endif
                        </div>
                    @endif

                    <div class="flex justify-end pt-2">
                        <button
                            wire:click="continueFromProgram"
                            @if (! $selectedProgram) disabled @endif
                            class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Continue <x-core::icon name="arrow-right" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            @endif

            <!-- Step 2: Personal Details -->
            @if ($step === 2)
                <form wire:submit="continueFromDetails" class="space-y-8">
                    <h2 class="text-2xl font-bold">Your Information</h2>

                    @if ($selectedProgram)
                        <div class="bg-brand-50 border border-brand-200 rounded-xl p-4 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-muted-foreground mb-0.5">Selected Program</p>
                                <p class="font-semibold text-sm">{{ $selectedProgram['title'] }}</p>
                                @if ($selectedCohort)
                                    <p class="text-xs text-brand-600">{{ $selectedCohort['name'] }}</p>
                                @endif
                            </div>
                            <button type="button" wire:click="goToStep(1)" class="text-xs text-brand-600 hover:underline">Change</button>
                        </div>
                    @endif

                    <div>
                        <h3 class="font-semibold text-base mb-4">Personal Information</h3>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">Full Name *</label>
                                <input wire:model="full_name" placeholder="Jane Wanjiku" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                                @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">Email Address *</label>
                                <input wire:model="email" type="email" placeholder="jane@example.com" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">Phone Number *</label>
                                <input wire:model="phone" data-clarity-mask="true" placeholder="+254 700 123 456" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">ID / Passport Number</label>
                                <input wire:model="id_number" data-clarity-mask="true" placeholder="12345678" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                            </div>
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">Gender</label>
                                <select wire:model="gender" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                                    <option value="">Select gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="prefer_not">Prefer not to say</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">Date of Birth</label>
                                <input wire:model="date_of_birth" type="date" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                            </div>
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">County *</label>
                                <select wire:model.live="county" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                                    <option value="">Select county</option>
                                    @foreach ($counties as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>
                                @error('county') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">Constituency *</label>
                                <select wire:model="constituency" @if (! $county) disabled @endif class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">{{ $county ? 'Select constituency' : 'Select a county first' }}</option>
                                    @foreach ($this->constituencyOptions() as $c)
                                        <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>
                                @error('constituency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-base mb-4">Professional Background</h3>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">Current Occupation</label>
                                <input wire:model="current_occupation" placeholder="Accountant / Student / Self-employed" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                            </div>
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">Employer / School</label>
                                <input wire:model="employer" placeholder="Company name" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                            </div>
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">Education Level</label>
                                <select wire:model="education_level" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                                    <option value="">Select level</option>
                                    <option value="secondary">Secondary (KCSE)</option>
                                    <option value="certificate">Certificate</option>
                                    <option value="diploma">Diploma</option>
                                    <option value="degree">Bachelor's Degree</option>
                                    <option value="postgraduate">Postgraduate</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">How did you hear about us?</label>
                                <select wire:model="referral_source" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                                    <option value="">Select source</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="twitter">Twitter/X</option>
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="google">Google Search</option>
                                    <option value="friend">Friend/Family</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="tiktok">TikTok</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-base mb-4">Emergency Contact</h3>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">Contact Name</label>
                                <input wire:model="emergency_contact_name" placeholder="Full name" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                            </div>
                            <div>
                                <label class="text-sm font-medium mb-1.5 block">Contact Phone</label>
                                <input wire:model="emergency_contact_phone" data-clarity-mask="true" placeholder="+254 700 000 000" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium mb-1.5 block">Special Requirements / Notes</label>
                        <textarea wire:model="special_requirements" placeholder="Any accessibility needs or special requirements..." rows="3" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background resize-none"></textarea>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" wire:click="goToStep(1)" class="inline-flex items-center gap-2 border border-border rounded-xl px-4 py-2.5 text-sm font-medium hover:bg-muted transition-colors">
                            <x-core::icon name="chevron-left" class="w-4 h-4" /> Back
                        </button>
                        <button type="submit" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors">
                            Continue <x-core::icon name="arrow-right" class="w-4 h-4" />
                        </button>
                    </div>
                </form>
            @endif

            <!-- Step 3: Payment -->
            @if ($step === 3)
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold">Payment Information</h2>

                    @if ($amount)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <p class="text-sm text-muted-foreground mb-1">Total Amount Due</p>
                            <p class="text-3xl font-black text-green-700">{{ $currency }} {{ number_format($amount) }}</p>
                        </div>
                    @endif

                    <div>
                        <h3 class="font-semibold mb-3">Select Payment Method</h3>
                        <div class="grid sm:grid-cols-3 gap-3">
                            @foreach ([
                                ['value' => 'mpesa', 'label' => 'M-Pesa', 'desc' => 'Safaricom M-Pesa', 'icon' => '📱'],
                                ['value' => 'stripe', 'label' => 'Card', 'desc' => 'Visa / Mastercard', 'icon' => '💳'],
                                ['value' => 'bank', 'label' => 'Bank Transfer', 'desc' => 'Direct bank transfer', 'icon' => '🏦'],
                            ] as $m)
                                <button
                                    type="button"
                                    wire:click="$set('paymentMethod', '{{ $m['value'] }}')"
                                    class="border-2 rounded-xl p-4 text-center transition-all hover:border-brand-500 {{ $paymentMethod === $m['value'] ? 'border-brand-500 bg-brand-50' : 'border-border' }}"
                                >
                                    <div class="text-2xl mb-1">{{ $m['icon'] }}</div>
                                    <p class="font-semibold text-sm">{{ $m['label'] }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $m['desc'] }}</p>
                                    @if ($paymentMethod === $m['value'])
                                        <x-core::icon name="check-circle" class="w-4 h-4 text-brand-500 mx-auto mt-1" />
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @if ($paymentMethod === 'mpesa')
                        <div class="bg-card border border-border rounded-xl p-5 space-y-4">
                            <h4 class="font-semibold">Pay with M-Pesa</h4>

                            @if ($mpesaCheckoutRequestId)
                                <livewire:payment::mpesa-status-poller :checkout-request-id="$mpesaCheckoutRequestId" :key="'mpesa-poll-'.$mpesaCheckoutRequestId" />
                            @else
                                <div>
                                    <label class="text-sm font-medium mb-1.5 block">Safaricom Phone Number</label>
                                    <input wire:model="mpesaPhone" placeholder="07XXXXXXXX" class="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                                    @error('mpesaPhone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                @if ($mpesaError)
                                    <p class="text-red-500 text-xs flex items-center gap-1"><x-core::icon name="x" class="w-3.5 h-3.5" />{{ $mpesaError }}</p>
                                @endif

                                <button
                                    type="button"
                                    wire:click="payNow"
                                    wire:loading.attr="disabled"
                                    wire:target="payNow"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors disabled:opacity-50"
                                >
                                    <span wire:loading.remove wire:target="payNow">Pay Now</span>
                                    <span wire:loading wire:target="payNow" class="inline-flex items-center gap-2">
                                        <span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                        Waiting for STK Push...
                                    </span>
                                </button>
                            @endif
                        </div>
                    @endif

                    @if ($paymentMethod === 'bank')
                        <div class="bg-card border border-border rounded-xl p-5">
                            <h4 class="font-semibold mb-3">Bank Transfer Details</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between"><span class="text-muted-foreground">Bank</span><span class="font-medium">{{ config('payment.bank.name') }}</span></div>
                                <div class="flex justify-between"><span class="text-muted-foreground">Account Name</span><span class="font-medium">{{ config('payment.bank.account_name') }}</span></div>
                                <div class="flex justify-between"><span class="text-muted-foreground">Account Number</span><span class="font-medium">{{ config('payment.bank.account_number') }}</span></div>
                                <div class="flex justify-between"><span class="text-muted-foreground">Branch</span><span class="font-medium">{{ config('payment.bank.branch') }}</span></div>
                            </div>
                        </div>
                    @endif

                    @if ($paymentMethod === 'stripe')
                        <div class="bg-muted rounded-xl p-5 text-center">
                            <p class="text-muted-foreground text-sm">Online card payment will be processed after booking confirmation.</p>
                        </div>
                    @endif

                    <div class="flex justify-between">
                        <button type="button" wire:click="goToStep(2)" class="inline-flex items-center gap-2 border border-border rounded-xl px-4 py-2.5 text-sm font-medium hover:bg-muted transition-colors">
                            <x-core::icon name="chevron-left" class="w-4 h-4" /> Back
                        </button>
                        @if ($paymentMethod !== 'mpesa')
                            <button wire:click="continueFromPayment" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors">
                                Review Booking <x-core::icon name="arrow-right" class="w-4 h-4" />
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Step 4: Review & Submit -->
            @if ($step === 4)
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold">Review & Confirm</h2>

                    <div class="bg-card border border-border rounded-2xl divide-y divide-border">
                        <div class="p-5">
                            <h3 class="font-semibold text-sm text-muted-foreground uppercase tracking-wider mb-3">Program</h3>
                            <p class="font-bold">{{ $selectedProgram['title'] ?? '' }}</p>
                            @if ($selectedCohort)
                                <p class="text-sm text-brand-600 mt-0.5">{{ $selectedCohort['name'] }}</p>
                            @endif
                            <p class="text-lg font-black mt-2">{{ $currency }} {{ $amount ? number_format($amount) : '' }}</p>
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-sm text-muted-foreground uppercase tracking-wider mb-3">Personal Details</h3>
                            <div class="grid sm:grid-cols-2 gap-2 text-sm">
                                <div><span class="text-muted-foreground">Name: </span>{{ $full_name }}</div>
                                <div data-clarity-mask="true"><span class="text-muted-foreground">Email: </span>{{ $email }}</div>
                                <div data-clarity-mask="true"><span class="text-muted-foreground">Phone: </span>{{ $phone }}</div>
                                <div><span class="text-muted-foreground">County: </span>{{ $county ?: '—' }}</div>
                                <div><span class="text-muted-foreground">Constituency: </span>{{ $constituency ?: '—' }}</div>
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-sm text-muted-foreground uppercase tracking-wider mb-3">Payment</h3>
                            <div class="text-sm">
                                <div><span class="text-muted-foreground">Method: </span><span class="capitalize font-medium">{{ $paymentMethod }}</span></div>
                                @if ($paymentReference)
                                    <div data-clarity-mask="true"><span class="text-muted-foreground">Reference: </span>{{ $paymentReference }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" wire:model="consent_given" class="mt-1 w-4 h-4 rounded border-border text-brand-500 focus:ring-brand-500">
                            <span class="text-sm text-foreground">
                                I confirm that the information provided is accurate and I agree to the
                                <a href="{{ url('/terms') }}" class="text-brand-600 hover:underline">Terms &amp; Conditions</a>,
                                <a href="{{ url('/privacy') }}" class="text-brand-600 hover:underline">Privacy Policy</a>, and
                                <a href="{{ url('/refund') }}" class="text-brand-600 hover:underline">Refund Policy</a> of Kazilink Digital Academy.
                            </span>
                        </label>
                        @error('consent_given') <p class="text-red-500 text-xs mt-2 flex items-center gap-1"><x-core::icon name="x" class="w-3.5 h-3.5" />{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-between">
                        <button type="button" wire:click="goToStep(3)" class="inline-flex items-center gap-2 border border-border rounded-xl px-4 py-2.5 text-sm font-medium hover:bg-muted transition-colors">
                            <x-core::icon name="chevron-left" class="w-4 h-4" /> Back
                        </button>
                        <button wire:click="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-semibold px-5 py-2.5 rounded-xl transition-colors min-w-[160px] justify-center disabled:opacity-50">
                            <span wire:loading.remove wire:target="submit" class="inline-flex items-center gap-2"><x-core::icon name="check-circle" class="w-4 h-4" /> Submit Booking</span>
                            <span wire:loading wire:target="submit">Submitting...</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
