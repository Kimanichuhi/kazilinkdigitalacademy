'use client';
import { useEffect, useState } from 'react';
import Link from 'next/link';
import { supabase } from '@/lib/supabase';
import {
  ArrowRight, Play, CheckCircle, TrendingUp, Users, Award, Globe,
  ChevronLeft, ChevronRight, BookOpen, Star, Zap, DollarSign
} from 'lucide-react';
import { ProgramCard } from '@/components/shared/ProgramCard';
import { TestimonialCard } from '@/components/shared/TestimonialCard';
import { CohortCard } from '@/components/shared/CohortCard';
import { CardSkeleton } from '@/components/shared/Skeleton';
import type { Program, Testimonial, Cohort, Trainer, Statistic, CTA, Advertisement, SiteSettingKV } from '@/lib/database.types';
import { cn } from '@/lib/utils';

const iconMap: Record<string, React.ReactNode> = {
  Users: <Users className="w-7 h-7" />,
  BookOpen: <BookOpen className="w-7 h-7" />,
  TrendingUp: <TrendingUp className="w-7 h-7" />,
  Globe: <Globe className="w-7 h-7" />,
  Award: <Award className="w-7 h-7" />,
  DollarSign: <DollarSign className="w-7 h-7" />,
  Zap: <Zap className="w-7 h-7" />,
};

export default function HomePage() {
  const [settings, setSettings] = useState<Record<string, string>>({});
  const [programs, setPrograms] = useState<Program[]>([]);
  const [cohorts, setCohorts] = useState<(Cohort & { programs: Program; trainers: Trainer | null })[]>([]);
  const [testimonials, setTestimonials] = useState<Testimonial[]>([]);
  const [trainers, setTrainers] = useState<Trainer[]>([]);
  const [stats, setStats] = useState<Statistic[]>([]);
  const [cta, setCta] = useState<CTA | null>(null);
  const [ads, setAds] = useState<Advertisement[]>([]);
  const [activeAdIdx, setActiveAdIdx] = useState(0);
  const [loading, setLoading] = useState(true);
  const [testimonialsIdx, setTestimonialsIdx] = useState(0);

  useEffect(() => {
    async function loadData() {
      const [settingsRes, programsRes, cohortsRes, testimonialsRes, trainersRes, statsRes, ctaRes, adsRes] = await Promise.all([
        supabase.from('site_settings').select('key,value').in('key', [
          'hero_title', 'hero_subtitle', 'hero_cta_primary', 'hero_cta_secondary', 'hero_image_url', 'site_name',
        ]),
        supabase.from('programs').select('*').eq('is_featured', true).eq('is_published', true).order('order_index').limit(6),
        supabase.from('cohorts').select('*, programs(*), trainers(*)').in('status', ['open', 'upcoming']).order('start_date').limit(4),
        supabase.from('testimonials').select('*').eq('is_featured', true).eq('is_published', true).order('order_index').limit(6),
        supabase.from('trainers').select('*').eq('is_featured', true).eq('is_active', true).order('order_index').limit(4),
        supabase.from('statistics').select('*').eq('is_active', true).order('order_index'),
        supabase.from('ctas').select('*').contains('placement', ['homepage']).eq('is_active', true).order('priority', { ascending: false }).limit(1).maybeSingle(),
        supabase.from('advertisements').select('*').contains('placement', ['homepage']).eq('status', 'active').or(`publish_date.is.null,publish_date.lte.${new Date().toISOString()}`).order('priority', { ascending: false }).limit(5),
      ]);

      if (settingsRes.data) {
        const map: Record<string, string> = {};
        settingsRes.data.forEach((s: SiteSettingKV) => { if (s.value) map[s.key] = s.value; });
        setSettings(map);
      }
      if (programsRes.data) setPrograms(programsRes.data);
      if (cohortsRes.data) setCohorts(cohortsRes.data as (Cohort & { programs: Program; trainers: Trainer | null })[]);
      if (testimonialsRes.data) setTestimonials(testimonialsRes.data);
      if (trainersRes.data) setTrainers(trainersRes.data);
      if (statsRes.data) setStats(statsRes.data);
      if (ctaRes.data) setCta(ctaRes.data);
      if (adsRes.data) {
        const now = new Date();
        const valid = adsRes.data.filter(a => !a.expiry_date || new Date(a.expiry_date) > now);
        setAds(valid);
      }
      setLoading(false);
    }
    loadData();
  }, []);

  const visibleTestimonials = testimonials.slice(testimonialsIdx, testimonialsIdx + 3);
  const activeAd = ads[activeAdIdx];
  const statsByKey = Object.fromEntries(stats.filter(s => s.key).map(s => [s.key as string, s]));

  useEffect(() => {
    if (ads.length <= 1) return;
    const timer = setInterval(() => setActiveAdIdx(i => (i + 1) % ads.length), 6000);
    return () => clearInterval(timer);
  }, [ads.length]);

  return (
    <>
      {/* HERO */}
      <section className="relative min-h-[90vh] flex items-center overflow-hidden bg-gray-950">
        {settings.hero_image_url && (
          <>
            <div
              className="absolute inset-0 bg-cover bg-center bg-no-repeat"
              style={{ backgroundImage: `url(${settings.hero_image_url})` }}
            />
            <div className="absolute inset-0 bg-gradient-to-r from-gray-950 via-gray-950/80 to-gray-950/40" />
          </>
        )}
        {!settings.hero_image_url && (
          <div className="absolute inset-0 bg-gradient-to-br from-gray-950 via-navy-950 to-gray-950" />
        )}

        {/* Floating orbs */}
        <div className="absolute top-1/4 right-1/4 w-72 h-72 bg-brand-500/10 rounded-full blur-3xl" />
        <div className="absolute bottom-1/4 right-1/3 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl" />

        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
          <div className="max-w-3xl">
            {/* Badge */}
            <div className="inline-flex items-center gap-2 bg-brand-500/10 border border-brand-500/20 text-brand-400 text-sm font-medium px-4 py-2 rounded-full mb-6">
              <Zap className="w-3.5 h-3.5" />
              Kenya's #1 Online Income Academy
            </div>

            <h1 className="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black text-white mb-6 leading-[1.05]">
              {settings.hero_title || 'Learn. Earn.\nThrive Online.'}
            </h1>

            <p className="text-lg sm:text-xl text-gray-300 mb-8 leading-relaxed max-w-2xl">
              {settings.hero_subtitle || 'Master high-income digital skills through hands-on training. Join thousands of Kenyans earning from anywhere.'}
            </p>

            <div className="flex flex-col sm:flex-row gap-3 mb-10">
              <Link
                href="/programs"
                className="inline-flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold text-base px-7 py-4 rounded-xl transition-all duration-200 shadow-2xl shadow-brand-500/40 hover:shadow-brand-500/60 hover:scale-[1.02] active:scale-[0.98]"
              >
                {settings.hero_cta_primary || 'Explore Programs'}
                <ArrowRight className="w-4 h-4" />
              </Link>
              <Link
                href="/contact"
                className="inline-flex items-center justify-center gap-2 border-2 border-white/20 text-white hover:border-white/40 hover:bg-white/5 font-semibold text-base px-7 py-4 rounded-xl transition-all duration-200"
              >
                <Play className="w-4 h-4" />
                {settings.hero_cta_secondary || 'Book Free Consultation'}
              </Link>
            </div>

            {/* Social proof */}
            <div className="flex flex-wrap items-center gap-6 text-sm text-gray-400">
              <div className="flex items-center gap-2">
                <div className="flex -space-x-2">
                  {['3184338', '3796217', '1239291', '1681010'].map(id => (
                    <img key={id} src={`favicon.jpeg`} alt="" className="w-7 h-7 rounded-full border-2 border-gray-950 object-cover" />
                  ))}
                </div>
                <span>{statsByKey.students_trained?.value || '2,000+'} students trained</span>
              </div>
              <div className="flex items-center gap-1 text-amber-400">
                {[1,2,3,4,5].map(i => <Star key={i} className="w-3.5 h-3.5 fill-current" />)}
                <span className="text-gray-400 ml-1">{statsByKey.avg_rating?.value || '4.8/5'} rating</span>
              </div>
            </div>
          </div>
        </div>

        {/* Bottom wave */}
        <div className="absolute bottom-0 left-0 right-0">
          <svg viewBox="0 0 1440 80" fill="none" className="w-full h-16 sm:h-20">
            <path d="M0 80H1440V20C1200 80 960 0 720 40C480 80 240 0 0 20V80Z" className="fill-background" />
          </svg>
        </div>
      </section>

      {/* PROMOTIONAL AD BANNER — Below Hero */}
      {ads.length > 0 && activeAd && (
        <section className="relative overflow-hidden bg-gray-950 border-y border-brand-500/20">
          {activeAd.desktop_image_url && (
            <div
              className="absolute inset-0 bg-cover bg-center opacity-30"
              style={{ backgroundImage: `url(${activeAd.desktop_image_url})` }}
            />
          )}
          <div className="absolute inset-0 bg-gradient-to-r from-gray-950 via-gray-950/80 to-gray-950/50" />
          {/* Animated glow */}
          <div className="absolute top-0 left-0 w-96 h-96 bg-brand-500/10 rounded-full blur-3xl animate-pulse" />
          <div className="absolute bottom-0 right-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl" />

          <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
            <div className="flex flex-col lg:flex-row items-center gap-6">
              {/* Text content */}
              <div className="flex-1 min-w-0 text-center lg:text-left">
                {activeAd.subtitle && (
                  <div className="inline-flex items-center gap-1.5 bg-brand-500/15 border border-brand-500/30 text-brand-400 text-xs font-bold px-3 py-1 rounded-full mb-3 uppercase tracking-wider">
                    <Zap className="w-3 h-3" />
                    {activeAd.subtitle}
                  </div>
                )}
                <h2 className="text-2xl sm:text-3xl md:text-4xl font-black text-white mb-2 leading-tight">
                  {activeAd.title}
                </h2>
                {activeAd.description && (
                  <p className="text-sm sm:text-base text-gray-300 max-w-2xl mx-auto lg:mx-0">
                    {activeAd.description}
                  </p>
                )}
              </div>

              {/* CTA button */}
              {activeAd.cta_text && activeAd.cta_link && (
                <div className="flex-shrink-0 flex flex-col items-center gap-2">
                  <Link
                    href={activeAd.cta_link}
                    className="group inline-flex items-center gap-2 bg-gradient-to-r from-brand-500 to-navy-600 hover:from-brand-400 hover:to-blue-500 text-white font-bold text-base px-7 py-4 rounded-xl transition-all duration-200 shadow-2xl shadow-brand-500/40 hover:shadow-brand-500/60 hover:scale-105 active:scale-95"
                  >
                    {activeAd.cta_text}
                    <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                  </Link>
                  {ads.length > 1 && (
                    <div className="flex gap-1.5 mt-1">
                      {ads.map((_, i) => (
                        <button
                          key={i}
                          onClick={() => setActiveAdIdx(i)}
                          className={cn('h-1.5 rounded-full transition-all', i === activeAdIdx ? 'bg-brand-400 w-6' : 'bg-white/30 w-1.5 hover:bg-white/50')}
                        />
                      ))}
                    </div>
                  )}
                </div>
              )}
            </div>
          </div>
        </section>
      )}

      {/* STATS */}
      {stats.length > 0 && (
        <section className="py-12 bg-background">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
              {stats.map((stat) => (
                <div key={stat.id} className="text-center group">
                  <div className="flex justify-center mb-2 text-brand-500 group-hover:scale-110 transition-transform">
                    {stat.icon && iconMap[stat.icon] ? iconMap[stat.icon] : <TrendingUp className="w-7 h-7" />}
                  </div>
                  <div className="text-2xl sm:text-3xl font-black text-foreground mb-0.5">{stat.value}</div>
                  <div className="text-xs text-muted-foreground font-medium">{stat.label}</div>
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* FEATURED PROGRAMS */}
      <section className="section-padding bg-muted/30">
        <div className="max-w-7xl mx-auto container-padding">
          <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
            <div>
              <span className="text-brand-600 font-semibold text-sm uppercase tracking-wider mb-2 block">Our Programs</span>
              <h2 className="text-3xl sm:text-4xl font-black text-foreground">Learn Skills That Pay</h2>
              <p className="text-muted-foreground mt-2">Practical, results-driven training built for the real world.</p>
            </div>
            <Link href="/programs" className="flex items-center gap-1.5 text-brand-600 font-semibold hover:gap-2.5 transition-all whitespace-nowrap">
              View All Programs <ArrowRight className="w-4 h-4" />
            </Link>
          </div>

          <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {loading ? (
              Array.from({ length: 6 }).map((_, i) => <CardSkeleton key={i} />)
            ) : programs.length > 0 ? (
              programs.map(p => <ProgramCard key={p.id} program={p} />)
            ) : (
              <div className="col-span-3 text-center py-12 text-muted-foreground">No programs found.</div>
            )}
          </div>
        </div>
      </section>

      {/* UPCOMING COHORTS */}
      {cohorts.length > 0 && (
        <section className="section-padding bg-background">
          <div className="max-w-7xl mx-auto container-padding">
            <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
              <div>
                <span className="text-brand-600 font-semibold text-sm uppercase tracking-wider mb-2 block">Upcoming Cohorts</span>
                <h2 className="text-3xl sm:text-4xl font-black text-foreground">Next Intakes</h2>
                <p className="text-muted-foreground mt-2">Seats are limited. Book yours before they fill up.</p>
              </div>
              <Link href="/cohorts" className="flex items-center gap-1.5 text-brand-600 font-semibold hover:gap-2.5 transition-all whitespace-nowrap">
                View All Cohorts <ArrowRight className="w-4 h-4" />
              </Link>
            </div>

            <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
              {cohorts.map(c => (
                <CohortCard key={c.id} cohort={c} />
              ))}
            </div>
          </div>
        </section>
      )}

      {/* TRAINERS */}
      {trainers.length > 0 && (
        <section className="section-padding bg-muted/30">
          <div className="max-w-7xl mx-auto container-padding">
            <div className="text-center mb-10">
              <span className="text-brand-600 font-semibold text-sm uppercase tracking-wider mb-2 block">Expert Trainers</span>
              <h2 className="text-3xl sm:text-4xl font-black text-foreground">Learn From Real Practitioners</h2>
              <p className="text-muted-foreground mt-2 max-w-xl mx-auto">Our trainers don't just teach — they actively earn online using the exact skills they teach you.</p>
            </div>

            <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {trainers.map(trainer => (
                <div key={trainer.id} className="bg-card border border-border rounded-2xl p-5 text-center card-hover group">
                  <div className="relative inline-block mb-4">
                    {trainer.avatar_url ? (
                      <img src={trainer.avatar_url} alt={trainer.full_name} className="w-20 h-20 rounded-full object-cover mx-auto group-hover:ring-4 ring-brand-500/30 transition-all" />
                    ) : (
                      <div className="w-20 h-20 rounded-full bg-brand-500 flex items-center justify-center text-white text-2xl font-bold mx-auto">
                        {trainer.full_name[0]}
                      </div>
                    )}
                    <div className="absolute bottom-0 right-0 w-6 h-6 bg-green-500 rounded-full border-2 border-card flex items-center justify-center">
                      <CheckCircle className="w-3 h-3 text-white" />
                    </div>
                  </div>
                  <h3 className="font-bold text-foreground mb-1">{trainer.full_name}</h3>
                  {trainer.title && <p className="text-xs text-brand-600 font-medium mb-2">{trainer.title}</p>}
                  {trainer.rating > 0 && (
                    <div className="flex items-center justify-center gap-1 text-amber-500 text-xs">
                      <Star className="w-3.5 h-3.5 fill-current" />
                      {trainer.rating.toFixed(1)} ({trainer.review_count} reviews)
                    </div>
                  )}
                  {trainer.specializations && (
                    <div className="flex flex-wrap gap-1 justify-center mt-3">
                      {trainer.specializations.slice(0, 3).map(s => (
                        <span key={s} className="text-[10px] bg-brand-50 text-brand-700 px-2 py-0.5 rounded-full font-medium">
                          {s}
                        </span>
                      ))}
                    </div>
                  )}
                </div>
              ))}
            </div>

            <div className="text-center mt-8">
              <Link href="/about#trainers" className="inline-flex items-center gap-2 text-brand-600 font-semibold hover:gap-3 transition-all">
                Meet All Trainers <ArrowRight className="w-4 h-4" />
              </Link>
            </div>
          </div>
        </section>
      )}

      {/* TESTIMONIALS */}
      {testimonials.length > 0 && (
        <section className="section-padding bg-background">
          <div className="max-w-7xl mx-auto container-padding">
            <div className="text-center mb-10">
              <span className="text-brand-600 font-semibold text-sm uppercase tracking-wider mb-2 block">Student Success</span>
              <h2 className="text-3xl sm:text-4xl font-black text-foreground">Real Results. Real People.</h2>
              <p className="text-muted-foreground mt-2 max-w-xl mx-auto">Stories from graduates who transformed their income with Kazilink training.</p>
            </div>

            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {testimonials.slice(0, 3).map(t => (
                <TestimonialCard key={t.id} testimonial={t} />
              ))}
            </div>

            {testimonials.length > 3 && (
              <div className="text-center mt-8">
                <Link href="/success-stories" className="inline-flex items-center gap-2 text-brand-600 font-semibold hover:gap-3 transition-all">
                  Read More Success Stories <ArrowRight className="w-4 h-4" />
                </Link>
              </div>
            )}
          </div>
        </section>
      )}

      {/* CTA BANNER */}
      {cta && (
        <section
          className="py-20"
          style={{ backgroundColor: cta.background_color || '#0f172a' }}
        >
          {cta.background_image_url && (
            <div className="absolute inset-0 bg-cover bg-center opacity-20" style={{ backgroundImage: `url(${cta.background_image_url})` }} />
          )}
          <div className="relative max-w-4xl mx-auto text-center container-padding">
            <h2 className="text-3xl sm:text-4xl md:text-5xl font-black text-white mb-4">{cta.title}</h2>
            {cta.subtitle && <p className="text-lg text-white/80 mb-3">{cta.subtitle}</p>}
            {cta.description && <p className="text-white/60 mb-8 max-w-2xl mx-auto">{cta.description}</p>}
            <div className="flex flex-col sm:flex-row gap-3 justify-center">
              {cta.button_one_text && cta.button_one_link && (
                <Link
                  href={cta.button_one_link}
                  className="inline-flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold px-7 py-4 rounded-xl transition-all hover:scale-[1.02] shadow-2xl shadow-brand-500/40"
                >
                  {cta.button_one_text} <ArrowRight className="w-4 h-4" />
                </Link>
              )}
              {cta.button_two_text && cta.button_two_link && (
                <Link
                  href={cta.button_two_link}
                  className="inline-flex items-center justify-center gap-2 border-2 border-white/30 text-white hover:border-white/60 font-semibold px-7 py-4 rounded-xl transition-all"
                >
                  {cta.button_two_text}
                </Link>
              )}
            </div>
          </div>
        </section>
      )}
    </>
  );
}
