'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { useParams } from 'next/navigation';
import Link from 'next/link';
import {
  Clock, Users, Star, CheckCircle, ArrowRight, Calendar, MapPin,
  Award, BookOpen, ChevronDown, ChevronUp, Wifi
} from 'lucide-react';
import { CohortCard } from '@/components/shared/CohortCard';
import { TestimonialCard } from '@/components/shared/TestimonialCard';
import { ProgramCard } from '@/components/shared/ProgramCard';
import type { Program, Cohort, Trainer, Testimonial, FAQ } from '@/lib/database.types';
import { cn } from '@/lib/utils';

export default function ProgramDetailPage() {
  const params = useParams();
  const slug = params?.slug as string;
  const [program, setProgram] = useState<Program | null>(null);
  const [trainer, setTrainer] = useState<Trainer | null>(null);
  const [cohorts, setCohorts] = useState<Cohort[]>([]);
  const [testimonials, setTestimonials] = useState<Testimonial[]>([]);
  const [related, setRelated] = useState<Program[]>([]);
  const [faqs, setFaqs] = useState<FAQ[]>([]);
  const [loading, setLoading] = useState(true);
  const [openFaq, setOpenFaq] = useState<string | null>(null);
  const [activeTab, setActiveTab] = useState<'overview' | 'curriculum' | 'trainer' | 'reviews'>('overview');

  useEffect(() => {
    if (!slug) return;
    async function load() {
      const { data: prog } = await supabase.from('programs').select('*').eq('slug', slug).eq('is_published', true).maybeSingle();
      if (!prog) { setLoading(false); return; }
      setProgram(prog);

      const [cohortsRes, testimonialsRes, faqsRes, relatedRes] = await Promise.all([
        supabase.from('cohorts').select('*, trainers(*)').eq('program_id', prog.id).in('status', ['open', 'upcoming']).order('start_date'),
        supabase.from('testimonials').select('*').eq('program_id', prog.id).eq('is_published', true).order('order_index').limit(4),
        supabase.from('faqs').select('*').eq('is_published', true).order('order_index').limit(8),
        supabase.from('programs').select('*').eq('is_published', true).eq('category_id', prog.category_id!).neq('id', prog.id).limit(3),
      ]);

      if (cohortsRes.data) setCohorts(cohortsRes.data as Cohort[]);
      if (testimonialsRes.data) setTestimonials(testimonialsRes.data);
      if (faqsRes.data) setFaqs(faqsRes.data);
      if (relatedRes.data) setRelated(relatedRes.data);
      setLoading(false);
    }
    load();
  }, [slug]);

  if (!loading && !program) {
    return (
      <div className="max-w-3xl mx-auto px-4 py-20 text-center">
        <h1 className="text-2xl font-bold mb-4">Program Not Found</h1>
        <Link href="/programs" className="text-brand-600 hover:underline">← Back to Programs</Link>
      </div>
    );
  }

  if (loading) {
    return (
      <div className="animate-pulse max-w-7xl mx-auto px-4 py-16">
        <div className="h-8 bg-muted rounded w-64 mb-4" />
        <div className="h-4 bg-muted rounded w-full mb-2" />
        <div className="h-4 bg-muted rounded w-2/3" />
      </div>
    );
  }

  if (!program) return null;

  const curriculum = Array.isArray(program.curriculum) ? program.curriculum as { title: string; lessons: string[] }[] : [];

  return (
    <>
      {/* Hero */}
      <div className="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-12 sm:py-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col lg:flex-row gap-10">
            <div className="flex-1">
              <div className="flex items-center gap-2 mb-4">
                <span className="text-xs font-medium px-2.5 py-1 rounded-full bg-brand-500/20 text-brand-400 capitalize">
                  {program.level?.replace('_', ' ')}
                </span>
                <span className="text-gray-400 text-xs capitalize">{program.delivery_mode?.replace('_', ' ')}</span>
              </div>
              <h1 className="text-3xl sm:text-4xl lg:text-5xl font-black text-white mb-4 leading-tight">{program.title}</h1>
              {program.subtitle && <p className="text-lg text-gray-300 mb-5">{program.subtitle}</p>}

              <div className="flex flex-wrap gap-5 text-sm text-gray-400 mb-6">
                {program.duration_label && <span className="flex items-center gap-2"><Clock className="w-4 h-4 text-brand-400" />{program.duration_label}</span>}
                {program.enrollment_count > 0 && <span className="flex items-center gap-2"><Users className="w-4 h-4 text-brand-400" />{program.enrollment_count.toLocaleString()} enrolled</span>}
                {program.rating > 0 && (
                  <span className="flex items-center gap-2 text-amber-400">
                    <Star className="w-4 h-4 fill-current" />{program.rating.toFixed(1)} ({program.review_count} reviews)
                  </span>
                )}
              </div>

              {program.outcomes && program.outcomes.length > 0 && (
                <div className="space-y-2">
                  <p className="text-white font-semibold text-sm mb-3">What you'll achieve:</p>
                  <div className="grid sm:grid-cols-2 gap-2">
                    {program.outcomes.slice(0, 4).map((o, i) => (
                      <div key={i} className="flex items-start gap-2 text-sm text-gray-300">
                        <CheckCircle className="w-4 h-4 text-green-400 flex-shrink-0 mt-0.5" />
                        {o}
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>

            {/* Sticky enrollment card */}
            <div className="lg:w-80 xl:w-96">
              <div className="bg-card border border-border rounded-2xl overflow-hidden shadow-2xl">
                {program.thumbnail_url && (
                  <img src={program.thumbnail_url} alt={program.title} className="w-full h-48 object-cover" />
                )}
                <div className="p-5">
                  <div className="flex items-baseline gap-3 mb-4">
                    <span className="text-3xl font-black text-foreground">
                      {program.currency} {program.price?.toLocaleString()}
                    </span>
                    {program.original_price && program.original_price > (program.price || 0) && (
                      <span className="text-muted-foreground line-through text-sm">{program.original_price.toLocaleString()}</span>
                    )}
                  </div>

                  <Link
                    href={`/booking?program=${program.id}`}
                    className="w-full flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold py-3.5 rounded-xl transition-colors mb-3 shadow-lg shadow-brand-500/25"
                  >
                    Enroll Now <ArrowRight className="w-4 h-4" />
                  </Link>
                  <Link
                    href="/contact"
                    className="w-full flex items-center justify-center gap-2 border-2 border-border text-foreground hover:border-brand-500 hover:text-brand-600 font-semibold py-3.5 rounded-xl transition-colors text-sm"
                  >
                    Book Free Consultation
                  </Link>

                  <div className="mt-4 pt-4 border-t border-border space-y-2 text-sm text-muted-foreground">
                    {program.duration_label && <div className="flex justify-between"><span>Duration</span><span className="font-medium text-foreground">{program.duration_label}</span></div>}
                    <div className="flex justify-between"><span>Format</span><span className="font-medium text-foreground capitalize">{program.delivery_mode?.replace('_', ' ')}</span></div>
                    <div className="flex justify-between"><span>Level</span><span className="font-medium text-foreground capitalize">{program.level?.replace('_', ' ')}</span></div>
                    <div className="flex justify-between"><span>Certificate</span><span className="font-medium text-green-600 flex items-center gap-1"><Award className="w-3.5 h-3.5" />Included</span></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {/* Tabs */}
        <div className="flex gap-1 bg-muted rounded-xl p-1 mb-8 w-fit overflow-x-auto">
          {(['overview', 'curriculum', 'trainer', 'reviews'] as const).map(tab => (
            <button
              key={tab}
              onClick={() => setActiveTab(tab)}
              className={cn('px-4 py-2 text-sm font-medium rounded-lg transition-colors capitalize whitespace-nowrap', activeTab === tab ? 'bg-white text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground')}
            >
              {tab}
            </button>
          ))}
        </div>

        <div className="grid lg:grid-cols-3 gap-10">
          <div className="lg:col-span-2 space-y-10">
            {/* Overview */}
            {activeTab === 'overview' && (
              <>
                {program.description && (
                  <div>
                    <h2 className="text-2xl font-bold mb-4">About This Program</h2>
                    <p className="text-muted-foreground leading-relaxed">{program.description}</p>
                  </div>
                )}
                {program.outcomes && program.outcomes.length > 0 && (
                  <div>
                    <h2 className="text-2xl font-bold mb-4">What You'll Learn</h2>
                    <div className="grid sm:grid-cols-2 gap-3">
                      {program.outcomes.map((o, i) => (
                        <div key={i} className="flex items-start gap-2">
                          <CheckCircle className="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" />
                          <span className="text-sm text-foreground">{o}</span>
                        </div>
                      ))}
                    </div>
                  </div>
                )}
                {program.requirements && program.requirements.length > 0 && (
                  <div>
                    <h2 className="text-2xl font-bold mb-4">Requirements</h2>
                    <ul className="space-y-2">
                      {program.requirements.map((r, i) => (
                        <li key={i} className="flex items-start gap-2 text-sm text-muted-foreground">
                          <div className="w-1.5 h-1.5 rounded-full bg-brand-500 mt-2 flex-shrink-0" />
                          {r}
                        </li>
                      ))}
                    </ul>
                  </div>
                )}
              </>
            )}

            {/* Curriculum */}
            {activeTab === 'curriculum' && (
              <div>
                <h2 className="text-2xl font-bold mb-6">Program Curriculum</h2>
                {curriculum.length > 0 ? (
                  <div className="space-y-3">
                    {curriculum.map((module: { title: string; lessons: string[] }, i) => (
                      <div key={i} className="border border-border rounded-xl overflow-hidden">
                        <div className="flex items-center justify-between px-5 py-4 bg-muted/50 font-semibold text-sm">
                          <span>Week {i + 1}: {module.title}</span>
                          <span className="text-xs text-muted-foreground">{module.lessons?.length || 0} lessons</span>
                        </div>
                        {module.lessons && module.lessons.length > 0 && (
                          <div className="px-5 py-3 space-y-2">
                            {module.lessons.map((lesson: string, j: number) => (
                              <div key={j} className="flex items-center gap-2 text-sm text-muted-foreground">
                                <BookOpen className="w-3.5 h-3.5 text-brand-500" />{lesson}
                              </div>
                            ))}
                          </div>
                        )}
                      </div>
                    ))}
                  </div>
                ) : (
                  <p className="text-muted-foreground">Detailed curriculum available upon enrollment. Contact us for more information.</p>
                )}
              </div>
            )}

            {/* Trainer tab */}
            {activeTab === 'trainer' && (
              <div>
                <h2 className="text-2xl font-bold mb-6">Your Trainer</h2>
                <p className="text-muted-foreground">Trainer information is available for specific cohorts. Check the upcoming cohorts section below.</p>
              </div>
            )}

            {/* Reviews */}
            {activeTab === 'reviews' && (
              <div>
                <h2 className="text-2xl font-bold mb-6">Student Reviews</h2>
                {testimonials.length > 0 ? (
                  <div className="space-y-4">
                    {testimonials.map(t => <TestimonialCard key={t.id} testimonial={t} />)}
                  </div>
                ) : (
                  <p className="text-muted-foreground">No reviews yet for this program.</p>
                )}
              </div>
            )}

            {/* Upcoming Cohorts */}
            {cohorts.length > 0 && (
              <div>
                <h2 className="text-2xl font-bold mb-5">Upcoming Cohorts</h2>
                <div className="grid sm:grid-cols-2 gap-4">
                  {cohorts.map(c => (
                    <CohortCard key={c.id} cohort={c as Cohort & { programs: Program; trainers: Trainer | null }} />
                  ))}
                </div>
              </div>
            )}

            {/* FAQs */}
            {faqs.length > 0 && (
              <div>
                <h2 className="text-2xl font-bold mb-5">Frequently Asked Questions</h2>
                <div className="space-y-2">
                  {faqs.slice(0, 6).map(faq => (
                    <div key={faq.id} className="border border-border rounded-xl overflow-hidden">
                      <button
                        className="w-full flex items-center justify-between px-5 py-4 text-sm font-medium text-left hover:bg-muted/50 transition-colors"
                        onClick={() => setOpenFaq(openFaq === faq.id ? null : faq.id)}
                      >
                        {faq.question}
                        {openFaq === faq.id ? <ChevronUp className="w-4 h-4 flex-shrink-0" /> : <ChevronDown className="w-4 h-4 flex-shrink-0" />}
                      </button>
                      {openFaq === faq.id && (
                        <div className="px-5 pb-4 text-sm text-muted-foreground leading-relaxed border-t border-border pt-3">
                          {faq.answer}
                        </div>
                      )}
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>

          {/* Sidebar */}
          <div className="space-y-6">
            {related.length > 0 && (
              <div>
                <h3 className="font-bold text-lg mb-4">Related Programs</h3>
                <div className="space-y-4">
                  {related.map(p => <ProgramCard key={p.id} program={p} />)}
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </>
  );
}
