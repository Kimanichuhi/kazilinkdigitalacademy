'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { TestimonialCard } from '@/components/shared/TestimonialCard';
import { ArrowRight, Star, TrendingUp, GraduationCap, DollarSign } from 'lucide-react';
import type { Testimonial, Statistic } from '@/lib/database.types';
import Link from 'next/link';

const highlightIcons: Record<string, React.ReactNode> = {
  students_trained: <GraduationCap className="w-6 h-6 mx-auto" />,
  avg_rating: <Star className="w-6 h-6 mx-auto" />,
  success_rate: <TrendingUp className="w-6 h-6 mx-auto" />,
  avg_income_increase: <DollarSign className="w-6 h-6 mx-auto" />,
};

const highlightFallbacks: Record<string, { label: string; value: string }> = {
  students_trained: { label: 'Total Graduates', value: '2,000+' },
  avg_rating: { label: 'Avg Rating', value: '4.8/5' },
  success_rate: { label: 'Income Success Rate', value: '94%' },
  avg_income_increase: { label: 'Avg Income Increase', value: '3x' },
};

export default function SuccessStoriesPage() {
  const [testimonials, setTestimonials] = useState<Testimonial[]>([]);
  const [stats, setStats] = useState<Statistic[]>([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState<number | null>(null);

  useEffect(() => {
    Promise.all([
      supabase.from('testimonials').select('*').eq('is_published', true).order('order_index'),
      supabase.from('statistics').select('*').eq('is_active', true),
    ]).then(([testimonialsRes, statsRes]) => {
      if (testimonialsRes.data) setTestimonials(testimonialsRes.data);
      if (statsRes.data) setStats(statsRes.data);
      setLoading(false);
    });
  }, []);

  const statsByKey = Object.fromEntries(stats.filter(s => s.key).map(s => [s.key as string, s]));
  const highlights = Object.keys(highlightFallbacks).map(key => ({
    key,
    label: statsByKey[key]?.label || highlightFallbacks[key].label,
    value: statsByKey[key]?.value || highlightFallbacks[key].value,
  }));

  const filtered = filter ? testimonials.filter(t => t.rating === filter) : testimonials;

  return (
    <>
      <div className="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 text-center">
          <span className="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Proof It Works</span>
          <h1 className="text-4xl sm:text-5xl font-black text-white mb-4">Student Success Stories</h1>
          <p className="text-gray-400 text-lg">Real people. Real results. Real income transformations.</p>
          <div className="flex items-center justify-center gap-2 mt-4">
            {[5,4,3].map(r => (
              <button
                key={r}
                onClick={() => setFilter(filter === r ? null : r)}
                className={`flex items-center gap-1 px-3 py-1.5 rounded-full text-sm transition-colors ${filter === r ? 'bg-amber-400 text-gray-900' : 'bg-white/10 text-gray-300 hover:bg-white/20'}`}
              >
                {r} <Star className={`w-3.5 h-3.5 ${filter === r ? 'fill-current' : ''}`} />
              </button>
            ))}
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {loading ? (
          <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {Array.from({ length: 6 }).map((_, i) => <div key={i} className="h-64 skeleton rounded-2xl" />)}
          </div>
        ) : filtered.length > 0 ? (
          <>
            {/* Highlight stats */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-5 mb-12">
              {highlights.map(s => (
                <div key={s.key} className="bg-card border border-border rounded-2xl p-5 text-center">
                  <div className="text-brand-500 mb-2">{highlightIcons[s.key]}</div>
                  <div className="text-2xl font-black text-foreground">{s.value}</div>
                  <div className="text-xs text-muted-foreground mt-0.5">{s.label}</div>
                </div>
              ))}
            </div>

            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {filtered.map(t => <TestimonialCard key={t.id} testimonial={t} />)}
            </div>
          </>
        ) : (
          <div className="text-center py-20 text-muted-foreground">No testimonials found.</div>
        )}

        <div className="mt-16 bg-gradient-to-r from-brand-500 to-navy-600 rounded-2xl p-8 text-center text-white">
          <TrendingUp className="w-10 h-10 mx-auto mb-4 opacity-80" />
          <h2 className="text-2xl font-black mb-3">Your Success Story Starts Here</h2>
          <p className="text-brand-100 mb-6">Join thousands of Kenyans who invested in themselves and changed their income trajectory.</p>
          <Link href="/booking" className="inline-flex items-center gap-2 bg-white text-brand-600 font-bold px-7 py-3.5 rounded-xl hover:bg-brand-50 transition-colors">
            Start Your Journey <ArrowRight className="w-4 h-4" />
          </Link>
        </div>
      </div>
    </>
  );
}
