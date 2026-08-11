'use client';
import { useEffect, useState, Suspense } from 'react';
import { useSearchParams } from 'next/navigation';
import { supabase } from '@/lib/supabase';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { toast } from 'sonner';
import Link from 'next/link';
import {
  CheckCircle, ArrowRight, ArrowLeft, User, BookOpen, CreditCard,
  Shield, Upload, AlertCircle, Loader2, ChevronDown
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import type { Program, Cohort, Trainer } from '@/lib/database.types';
import { cn } from '@/lib/utils';
import { format } from 'date-fns';

const steps = [
  { id: 1, label: 'Program', icon: BookOpen },
  { id: 2, label: 'Details', icon: User },
  { id: 3, label: 'Payment', icon: CreditCard },
  { id: 4, label: 'Review', icon: Shield },
];

const personalSchema = z.object({
  full_name: z.string().min(2, 'Full name required'),
  email: z.string().email('Valid email required'),
  phone: z.string().min(9, 'Valid phone required'),
  id_number: z.string().optional(),
  date_of_birth: z.string().optional(),
  gender: z.string().optional(),
  nationality: z.string().optional(),
  address: z.string().optional(),
  city: z.string().optional(),
  country: z.string().default('Kenya'),
  current_occupation: z.string().optional(),
  employer: z.string().optional(),
  education_level: z.string().optional(),
  referral_source: z.string().optional(),
  emergency_contact_name: z.string().optional(),
  emergency_contact_phone: z.string().optional(),
  special_requirements: z.string().optional(),
  consent_given: z.boolean().refine(v => v === true, 'You must agree to the terms'),
});

type PersonalFormData = z.infer<typeof personalSchema>;

type CohortWithRelations = Cohort & { programs: Program; trainers: Trainer | null };

function BookingPageInner() {
  const searchParams = useSearchParams();
  const [step, setStep] = useState(1);
  const [programs, setPrograms] = useState<Program[]>([]);
  const [cohorts, setCohorts] = useState<CohortWithRelations[]>([]);
  const [selectedProgram, setSelectedProgram] = useState<Program | null>(null);
  const [selectedCohort, setSelectedCohort] = useState<CohortWithRelations | null>(null);
  const [paymentMethod, setPaymentMethod] = useState('mpesa');
  const [paymentReference, setPaymentReference] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [bookingResult, setBookingResult] = useState<{ bookingNumber: string } | null>(null);
  const [loadingCohorts, setLoadingCohorts] = useState(false);

  const { register, handleSubmit, formState: { errors }, watch, getValues } = useForm<PersonalFormData>({
    resolver: zodResolver(personalSchema),
    defaultValues: { country: 'Kenya', consent_given: false },
  });

  useEffect(() => {
    supabase.from('programs').select('*').eq('is_published', true).eq('is_active', true).order('order_index').then(({ data }) => {
      if (data) setPrograms(data);
    });

    const programId = searchParams?.get('program');
    const cohortId = searchParams?.get('cohort');

    if (programId) {
      supabase.from('programs').select('*').eq('id', programId).maybeSingle().then(({ data }) => {
        if (data) { setSelectedProgram(data); loadCohorts(data.id); }
      });
    }
    if (cohortId) {
      supabase.from('cohorts').select('*, programs(*), trainers(*)').eq('id', cohortId).maybeSingle().then(({ data }) => {
        if (data) {
          const c = data as CohortWithRelations;
          setSelectedCohort(c);
          setSelectedProgram(c.programs);
          if (step === 1) setStep(2);
        }
      });
    }
  }, []);

  async function loadCohorts(programId: string) {
    setLoadingCohorts(true);
    const { data } = await supabase
      .from('cohorts').select('*, programs(*), trainers(*)')
      .eq('program_id', programId)
      .in('status', ['open', 'upcoming'])
      .order('start_date');
    if (data) setCohorts(data as CohortWithRelations[]);
    setLoadingCohorts(false);
  }

  function handleProgramSelect(program: Program) {
    setSelectedProgram(program);
    setSelectedCohort(null);
    loadCohorts(program.id);
  }

  async function onSubmit(data: PersonalFormData) {
    if (!selectedProgram) { toast.error('Please select a program'); return; }
    setSubmitting(true);

    const bookingData = {
      program_id: selectedProgram.id,
      cohort_id: selectedCohort?.id || null,
      full_name: data.full_name,
      email: data.email,
      phone: data.phone,
      id_number: data.id_number || null,
      date_of_birth: data.date_of_birth || null,
      gender: data.gender || null,
      nationality: data.nationality || null,
      address: data.address || null,
      city: data.city || null,
      country: data.country,
      current_occupation: data.current_occupation || null,
      employer: data.employer || null,
      education_level: data.education_level || null,
      referral_source: data.referral_source || null,
      emergency_contact_name: data.emergency_contact_name || null,
      emergency_contact_phone: data.emergency_contact_phone || null,
      special_requirements: data.special_requirements || null,
      consent_given: data.consent_given,
      payment_method: paymentMethod,
      payment_reference: paymentReference || null,
      total_amount: selectedCohort?.price || selectedProgram?.price || null,
      currency: selectedProgram?.currency || 'KES',
      status: paymentReference ? 'awaiting_payment' : 'draft',
      payment_status: 'pending',
    };

    const { data: booking, error } = await supabase.from('bookings').insert(bookingData).select('booking_number').maybeSingle();

    if (error) {
      toast.error('Failed to submit booking. Please try again.');
      setSubmitting(false);
      return;
    }

    setBookingResult({ bookingNumber: booking?.booking_number || 'N/A' });
    setStep(5);
    setSubmitting(false);
  }

  if (step === 5 && bookingResult) {
    return (
      <div className="min-h-[60vh] flex items-center justify-center p-8">
        <div className="max-w-lg w-full text-center">
          <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <CheckCircle className="w-10 h-10 text-green-600" />
          </div>
          <h1 className="text-3xl font-black text-foreground mb-3">Booking Submitted!</h1>
          <p className="text-muted-foreground mb-2">
            Your booking reference is:
          </p>
          <div className="bg-brand-50 border border-brand-200 rounded-xl px-6 py-4 mb-6">
            <p className="text-2xl font-mono font-bold text-brand-600">
              {bookingResult.bookingNumber}
            </p>
          </div>
          <p className="text-sm text-muted-foreground mb-8">
            We've received your booking. Our team will review your application and contact you within 24 hours with payment confirmation and next steps.
          </p>
          <div className="flex flex-col sm:flex-row gap-3 justify-center">
            <Link href="/student" className="btn-primary inline-flex items-center gap-2 justify-center">
              Go to My Dashboard <ArrowRight className="w-4 h-4" />
            </Link>
            <Link href="/programs" className="btn-outline inline-flex items-center gap-2 justify-center">
              Browse More Programs
            </Link>
          </div>
        </div>
      </div>
    );
  }

  const amount = selectedCohort?.price || selectedProgram?.price;
  const currency = selectedProgram?.currency || 'KES';

  return (
    <>
      {/* Header */}
      <div className="bg-gradient-to-r from-gray-950 to-blue-950 py-10">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 text-center">
          <h1 className="text-3xl font-black text-white mb-2">Book Your Training</h1>
          <p className="text-gray-400">Complete the form below to secure your spot</p>
        </div>
      </div>

      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Stepper */}
        <div className="flex items-center justify-center gap-2 mb-10">
          {steps.map((s, i) => {
            const Icon = s.icon;
            const active = step === s.id;
            const done = step > s.id;
            return (
              <div key={s.id} className="flex items-center">
                <div className={cn(
                  'flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium transition-all',
                  active ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/30' :
                    done ? 'bg-green-500 text-white' :
                      'bg-muted text-muted-foreground'
                )}>
                  {done ? <CheckCircle className="w-4 h-4" /> : <Icon className="w-4 h-4" />}
                  <span className="hidden sm:inline">{s.label}</span>
                </div>
                {i < steps.length - 1 && <div className={cn('w-8 h-0.5 mx-1', done ? 'bg-green-500' : 'bg-border')} />}
              </div>
            );
          })}
        </div>

        {/* Step 1: Program Selection */}
        {step === 1 && (
          <div className="space-y-6 animate-fade-in">
            <h2 className="text-2xl font-bold">Choose Your Program</h2>

            <div className="grid sm:grid-cols-2 gap-4">
              {programs.map(p => (
                <button
                  key={p.id}
                  onClick={() => handleProgramSelect(p)}
                  className={cn(
                    'text-left border-2 rounded-2xl p-4 transition-all hover:border-brand-500',
                    selectedProgram?.id === p.id ? 'border-brand-500 bg-brand-50' : 'border-border'
                  )}
                >
                  <div className="flex gap-3">
                    {p.thumbnail_url && (
                      <img src={p.thumbnail_url} alt={p.title} className="w-16 h-12 rounded-lg object-cover flex-shrink-0" />
                    )}
                    <div className="flex-1 min-w-0">
                      <h3 className="font-semibold text-sm line-clamp-2">{p.title}</h3>
                      <p className="text-xs text-muted-foreground mt-0.5">{p.duration_label}</p>
                      <p className="text-sm font-bold text-brand-600 mt-1">{p.currency} {p.price?.toLocaleString()}</p>
                    </div>
                    {selectedProgram?.id === p.id && <CheckCircle className="w-5 h-5 text-brand-500 flex-shrink-0" />}
                  </div>
                </button>
              ))}
            </div>

            {selectedProgram && (
              <div>
                <h3 className="font-bold text-lg mb-4">Choose a Cohort (Optional)</h3>
                {loadingCohorts ? (
                  <div className="text-center py-6 text-muted-foreground">Loading available cohorts...</div>
                ) : cohorts.length > 0 ? (
                  <div className="grid sm:grid-cols-2 gap-3">
                    {cohorts.map(c => {
                      const seatsLeft = c.total_seats - c.booked_seats;
                      return (
                        <button
                          key={c.id}
                          onClick={() => setSelectedCohort(selectedCohort?.id === c.id ? null : c)}
                          className={cn(
                            'text-left border-2 rounded-xl p-4 transition-all hover:border-brand-500',
                            selectedCohort?.id === c.id ? 'border-brand-500 bg-brand-50' : 'border-border'
                          )}
                        >
                          <p className="font-semibold text-sm">{c.name}</p>
                          <p className="text-xs text-muted-foreground mt-1">{format(new Date(c.start_date), 'dd MMM yyyy')}</p>
                          <p className="text-xs text-muted-foreground">{c.schedule_details}</p>
                          <div className="flex justify-between items-center mt-2">
                            <span className={cn('text-xs font-medium', seatsLeft <= 5 ? 'text-red-500' : 'text-green-600')}>
                              {seatsLeft} seats left
                            </span>
                            {selectedCohort?.id === c.id && <CheckCircle className="w-4 h-4 text-brand-500" />}
                          </div>
                        </button>
                      );
                    })}
                  </div>
                ) : (
                  <p className="text-muted-foreground text-sm">No open cohorts available. Your booking will be placed for the next available cohort.</p>
                )}
              </div>
            )}

            <div className="flex justify-end pt-2">
              <Button
                onClick={() => setStep(2)}
                disabled={!selectedProgram}
                className="bg-brand-500 hover:bg-brand-600 text-white gap-2"
              >
                Continue <ArrowRight className="w-4 h-4" />
              </Button>
            </div>
          </div>
        )}

        {/* Step 2: Personal Details */}
        {step === 2 && (
          <form className="space-y-8 animate-fade-in" onSubmit={e => { e.preventDefault(); setStep(3); }}>
            <h2 className="text-2xl font-bold">Your Information</h2>

            {/* Selected program summary */}
            {selectedProgram && (
              <div className="bg-brand-50 border border-brand-200 rounded-xl p-4 flex items-center justify-between">
                <div>
                  <p className="text-xs text-muted-foreground mb-0.5">Selected Program</p>
                  <p className="font-semibold text-sm">{selectedProgram.title}</p>
                  {selectedCohort && <p className="text-xs text-brand-600">{selectedCohort.name}</p>}
                </div>
                <button type="button" onClick={() => { setStep(1); setSelectedCohort(null); }} className="text-xs text-brand-600 hover:underline">Change</button>
              </div>
            )}

            {/* Personal Info */}
            <div>
              <h3 className="font-semibold text-base mb-4">Personal Information</h3>
              <div className="grid sm:grid-cols-2 gap-4">
                <div>
                  <label className="text-sm font-medium mb-1.5 block">Full Name *</label>
                  <input {...register('full_name')} placeholder="Jane Wanjiku" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
                  {errors.full_name && <p className="text-red-500 text-xs mt-1">{errors.full_name.message}</p>}
                </div>
                <div>
                  <label className="text-sm font-medium mb-1.5 block">Email Address *</label>
                  <input {...register('email')} type="email" placeholder="jane@example.com" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
                  {errors.email && <p className="text-red-500 text-xs mt-1">{errors.email.message}</p>}
                </div>
                <div>
                  <label className="text-sm font-medium mb-1.5 block">Phone Number *</label>
                  <input {...register('phone')} placeholder="+254 700 123 456" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
                  {errors.phone && <p className="text-red-500 text-xs mt-1">{errors.phone.message}</p>}
                </div>
                <div>
                  <label className="text-sm font-medium mb-1.5 block">ID / Passport Number</label>
                  <input {...register('id_number')} placeholder="12345678" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
                </div>
                <div>
                  <label className="text-sm font-medium mb-1.5 block">Gender</label>
                  <select {...register('gender')} className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                    <option value="">Select gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="prefer_not">Prefer not to say</option>
                  </select>
                </div>
                <div>
                  <label className="text-sm font-medium mb-1.5 block">Date of Birth</label>
                  <input {...register('date_of_birth')} type="date" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
                </div>
                <div>
                  <label className="text-sm font-medium mb-1.5 block">City</label>
                  <input {...register('city')} placeholder="Nairobi" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
                </div>
                <div>
                  <label className="text-sm font-medium mb-1.5 block">Country</label>
                  <input {...register('country')} placeholder="Kenya" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
                </div>
              </div>
            </div>

            {/* Professional */}
            <div>
              <h3 className="font-semibold text-base mb-4">Professional Background</h3>
              <div className="grid sm:grid-cols-2 gap-4">
                <div>
                  <label className="text-sm font-medium mb-1.5 block">Current Occupation</label>
                  <input {...register('current_occupation')} placeholder="Accountant / Student / Self-employed" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
                </div>
                <div>
                  <label className="text-sm font-medium mb-1.5 block">Employer / School</label>
                  <input {...register('employer')} placeholder="Company name" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
                </div>
                <div>
                  <label className="text-sm font-medium mb-1.5 block">Education Level</label>
                  <select {...register('education_level')} className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                    <option value="">Select level</option>
                    <option value="secondary">Secondary (KCSE)</option>
                    <option value="certificate">Certificate</option>
                    <option value="diploma">Diploma</option>
                    <option value="degree">Bachelor's Degree</option>
                    <option value="postgraduate">Postgraduate</option>
                  </select>
                </div>
                <div>
                  <label className="text-sm font-medium mb-1.5 block">How did you hear about us?</label>
                  <select {...register('referral_source')} className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
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

            {/* Emergency Contact */}
            <div>
              <h3 className="font-semibold text-base mb-4">Emergency Contact</h3>
              <div className="grid sm:grid-cols-2 gap-4">
                <div>
                  <label className="text-sm font-medium mb-1.5 block">Contact Name</label>
                  <input {...register('emergency_contact_name')} placeholder="Full name" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
                </div>
                <div>
                  <label className="text-sm font-medium mb-1.5 block">Contact Phone</label>
                  <input {...register('emergency_contact_phone')} placeholder="+254 700 000 000" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
                </div>
              </div>
            </div>

            {/* Special Requirements */}
            <div>
              <label className="text-sm font-medium mb-1.5 block">Special Requirements / Notes</label>
              <textarea {...register('special_requirements')} placeholder="Any accessibility needs or special requirements..." rows={3} className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background resize-none" />
            </div>

            <div className="flex justify-between">
              <Button type="button" variant="outline" onClick={() => setStep(1)} className="gap-2">
                <ArrowLeft className="w-4 h-4" /> Back
              </Button>
              <Button type="submit" className="bg-brand-500 hover:bg-brand-600 text-white gap-2">
                Continue <ArrowRight className="w-4 h-4" />
              </Button>
            </div>
          </form>
        )}

        {/* Step 3: Payment */}
        {step === 3 && (
          <div className="space-y-6 animate-fade-in">
            <h2 className="text-2xl font-bold">Payment Information</h2>

            {amount && (
              <div className="bg-green-50 border border-green-200 rounded-xl p-4">
                <p className="text-sm text-muted-foreground mb-1">Total Amount Due</p>
                <p className="text-3xl font-black text-green-700">{currency} {amount.toLocaleString()}</p>
              </div>
            )}

            <div>
              <h3 className="font-semibold mb-3">Select Payment Method</h3>
              <div className="grid sm:grid-cols-3 gap-3">
                {[
                  { value: 'mpesa', label: 'M-Pesa', desc: 'Safaricom M-Pesa', icon: '📱' },
                  { value: 'stripe', label: 'Card', desc: 'Visa / Mastercard', icon: '💳' },
                  { value: 'bank', label: 'Bank Transfer', desc: 'Direct bank transfer', icon: '🏦' },
                ].map(m => (
                  <button
                    key={m.value}
                    onClick={() => setPaymentMethod(m.value)}
                    className={cn(
                      'border-2 rounded-xl p-4 text-center transition-all hover:border-brand-500',
                      paymentMethod === m.value ? 'border-brand-500 bg-brand-50' : 'border-border'
                    )}
                  >
                    <div className="text-2xl mb-1">{m.icon}</div>
                    <p className="font-semibold text-sm">{m.label}</p>
                    <p className="text-xs text-muted-foreground">{m.desc}</p>
                    {paymentMethod === m.value && <CheckCircle className="w-4 h-4 text-brand-500 mx-auto mt-1" />}
                  </button>
                ))}
              </div>
            </div>

            {paymentMethod === 'mpesa' && (
              <div className="bg-card border border-border rounded-xl p-5 space-y-4">
                <h4 className="font-semibold">M-Pesa Payment Instructions</h4>
                <ol className="text-sm text-muted-foreground space-y-2 list-decimal list-inside">
                  <li>Go to M-Pesa → Lipa na M-Pesa → Paybill</li>
                  <li>Business Number: <strong className="text-foreground">123456</strong></li>
                  <li>Account Number: Your phone number</li>
                  <li>Amount: <strong className="text-foreground">{currency} {amount?.toLocaleString()}</strong></li>
                  <li>Enter your M-Pesa PIN and confirm</li>
                </ol>
                <div>
                  <label className="text-sm font-medium mb-1.5 block">M-Pesa Transaction Code (optional)</label>
                  <input
                    value={paymentReference}
                    onChange={e => setPaymentReference(e.target.value.toUpperCase())}
                    placeholder="e.g. QHX7Y8Z9KA"
                    className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background uppercase"
                  />
                  <p className="text-xs text-muted-foreground mt-1">Enter your M-Pesa confirmation code if you have already paid</p>
                </div>
              </div>
            )}

            {paymentMethod === 'bank' && (
              <div className="bg-card border border-border rounded-xl p-5">
                <h4 className="font-semibold mb-3">Bank Transfer Details</h4>
                <div className="space-y-2 text-sm">
                  <div className="flex justify-between"><span className="text-muted-foreground">Bank</span><span className="font-medium">Equity Bank Kenya</span></div>
                  <div className="flex justify-between"><span className="text-muted-foreground">Account Name</span><span className="font-medium">Kazilink Digital Academy Ltd</span></div>
                  <div className="flex justify-between"><span className="text-muted-foreground">Account Number</span><span className="font-medium">0123456789</span></div>
                  <div className="flex justify-between"><span className="text-muted-foreground">Branch</span><span className="font-medium">Westlands, Nairobi</span></div>
                </div>
              </div>
            )}

            {paymentMethod === 'stripe' && (
              <div className="bg-muted rounded-xl p-5 text-center">
                <p className="text-muted-foreground text-sm">Online card payment will be processed after booking confirmation.</p>
              </div>
            )}

            <div className="flex justify-between">
              <Button variant="outline" onClick={() => setStep(2)} className="gap-2">
                <ArrowLeft className="w-4 h-4" /> Back
              </Button>
              <Button onClick={() => setStep(4)} className="bg-brand-500 hover:bg-brand-600 text-white gap-2">
                Review Booking <ArrowRight className="w-4 h-4" />
              </Button>
            </div>
          </div>
        )}

        {/* Step 4: Review & Submit */}
        {step === 4 && (
          <div className="space-y-6 animate-fade-in">
            <h2 className="text-2xl font-bold">Review & Confirm</h2>

            <div className="bg-card border border-border rounded-2xl divide-y divide-border">
              <div className="p-5">
                <h3 className="font-semibold text-sm text-muted-foreground uppercase tracking-wider mb-3">Program</h3>
                <p className="font-bold">{selectedProgram?.title}</p>
                {selectedCohort && <p className="text-sm text-brand-600 mt-0.5">{selectedCohort.name}</p>}
                <p className="text-lg font-black mt-2">{currency} {amount?.toLocaleString()}</p>
              </div>
              <div className="p-5">
                <h3 className="font-semibold text-sm text-muted-foreground uppercase tracking-wider mb-3">Personal Details</h3>
                <div className="grid sm:grid-cols-2 gap-2 text-sm">
                  <div><span className="text-muted-foreground">Name: </span>{getValues('full_name')}</div>
                  <div><span className="text-muted-foreground">Email: </span>{getValues('email')}</div>
                  <div><span className="text-muted-foreground">Phone: </span>{getValues('phone')}</div>
                  <div><span className="text-muted-foreground">City: </span>{getValues('city') || '—'}</div>
                </div>
              </div>
              <div className="p-5">
                <h3 className="font-semibold text-sm text-muted-foreground uppercase tracking-wider mb-3">Payment</h3>
                <div className="text-sm">
                  <div><span className="text-muted-foreground">Method: </span><span className="capitalize font-medium">{paymentMethod}</span></div>
                  {paymentReference && <div><span className="text-muted-foreground">Reference: </span>{paymentReference}</div>}
                </div>
              </div>
            </div>

            {/* Consent */}
            <div className="bg-amber-50 border border-amber-200 rounded-xl p-4">
              <label className="flex items-start gap-3 cursor-pointer">
                <input
                  type="checkbox"
                  {...register('consent_given')}
                  className="mt-1 w-4 h-4 rounded border-border text-brand-500 focus:ring-brand-500"
                />
                <span className="text-sm text-foreground">
                  I confirm that the information provided is accurate and I agree to the{' '}
                  <Link href="/terms" className="text-brand-600 hover:underline">Terms & Conditions</Link>
                  {', '}
                  <Link href="/privacy" className="text-brand-600 hover:underline">Privacy Policy</Link>
                  {', and '}
                  <Link href="/refund" className="text-brand-600 hover:underline">Refund Policy</Link>
                  {' of Kazilink Digital Academy.'}
                </span>
              </label>
              {errors.consent_given && <p className="text-red-500 text-xs mt-2 flex items-center gap-1"><AlertCircle className="w-3.5 h-3.5" />{errors.consent_given.message}</p>}
            </div>

            <div className="flex justify-between">
              <Button variant="outline" onClick={() => setStep(3)} className="gap-2">
                <ArrowLeft className="w-4 h-4" /> Back
              </Button>
              <Button
                onClick={handleSubmit(onSubmit)}
                disabled={submitting}
                className="bg-brand-500 hover:bg-brand-600 text-white gap-2 min-w-[160px]"
              >
                {submitting ? (
                  <><Loader2 className="w-4 h-4 animate-spin" /> Submitting...</>
                ) : (
                  <><CheckCircle className="w-4 h-4" /> Submit Booking</>
                )}
              </Button>
            </div>
          </div>
        )}
      </div>
    </>
  );
}

export default function BookingPage() {
  return (
    <Suspense fallback={<div className="flex items-center justify-center min-h-screen"><Loader2 className="w-8 h-8 animate-spin text-brand-500" /></div>}>
      <BookingPageInner />
    </Suspense>
  );
}
