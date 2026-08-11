'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { ChevronDown, ChevronUp, Search } from 'lucide-react';
import type { FAQ } from '@/lib/database.types';
import { cn } from '@/lib/utils';
import Link from 'next/link';
import { ArrowRight } from 'lucide-react';

export default function FAQPage() {
  const [faqs, setFaqs] = useState<FAQ[]>([]);
  const [loading, setLoading] = useState(true);
  const [openId, setOpenId] = useState<string | null>(null);
  const [search, setSearch] = useState('');
  const [category, setCategory] = useState('');

  useEffect(() => {
    supabase.from('faqs').select('*').eq('is_published', true).order('order_index').then(({ data }) => {
      if (data) setFaqs(data);
      setLoading(false);
    });
  }, []);

  const categories = [...new Set(faqs.map(f => f.category))];
  const filtered = faqs.filter(f =>
    (!category || f.category === category) &&
    (!search || f.question.toLowerCase().includes(search.toLowerCase()) || f.answer.toLowerCase().includes(search.toLowerCase()))
  );

  const groupedFaqs = categories.reduce((acc, cat) => {
    const items = filtered.filter(f => f.category === cat);
    if (items.length > 0) acc[cat] = items;
    return acc;
  }, {} as Record<string, FAQ[]>);

  return (
    <>
      <div className="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 text-center">
          <span className="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Support</span>
          <h1 className="text-4xl sm:text-5xl font-black text-white mb-4">Frequently Asked Questions</h1>
          <p className="text-gray-400 text-lg">Everything you need to know about Kazilink Digital Academy.</p>
          <div className="relative max-w-lg mx-auto mt-6">
            <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input
              type="text"
              placeholder="Search FAQs..."
              value={search}
              onChange={e => setSearch(e.target.value)}
              className="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 text-white placeholder-gray-400 rounded-xl focus:outline-none focus:border-brand-400 transition-colors"
            />
          </div>
        </div>
      </div>

      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {/* Category filter */}
        {categories.length > 0 && (
          <div className="flex gap-2 flex-wrap mb-8">
            <button onClick={() => setCategory('')} className={cn('text-sm font-medium px-4 py-2 rounded-full transition-colors', !category ? 'bg-brand-500 text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80')}>
              All
            </button>
            {categories.map(cat => (
              <button
                key={cat}
                onClick={() => setCategory(category === cat ? '' : cat)}
                className={cn('text-sm font-medium px-4 py-2 rounded-full transition-colors capitalize', category === cat ? 'bg-brand-500 text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80')}
              >
                {cat}
              </button>
            ))}
          </div>
        )}

        {loading ? (
          <div className="space-y-3">
            {Array.from({ length: 8 }).map((_, i) => (
              <div key={i} className="h-16 skeleton rounded-xl" />
            ))}
          </div>
        ) : (
          <div className="space-y-10">
            {Object.entries(groupedFaqs).map(([cat, items]) => (
              <div key={cat}>
                <h2 className="text-lg font-bold capitalize mb-4 text-brand-600">{cat}</h2>
                <div className="space-y-2">
                  {items.map(faq => (
                    <div key={faq.id} className="border border-border rounded-xl overflow-hidden">
                      <button
                        className="w-full flex items-center justify-between px-5 py-4 text-sm font-medium text-left hover:bg-muted/50 transition-colors"
                        onClick={() => setOpenId(openId === faq.id ? null : faq.id)}
                      >
                        <span>{faq.question}</span>
                        {openId === faq.id ? (
                          <ChevronUp className="w-4 h-4 flex-shrink-0 text-brand-500" />
                        ) : (
                          <ChevronDown className="w-4 h-4 flex-shrink-0 text-muted-foreground" />
                        )}
                      </button>
                      {openId === faq.id && (
                        <div className="px-5 pb-5 pt-3 border-t border-border text-sm text-muted-foreground leading-relaxed">
                          {faq.answer}
                        </div>
                      )}
                    </div>
                  ))}
                </div>
              </div>
            ))}

            {filtered.length === 0 && (
              <div className="text-center py-16 text-muted-foreground">
                <Search className="w-12 h-12 mx-auto mb-4 opacity-30" />
                <p className="text-lg font-medium">No FAQs found</p>
                <p className="text-sm">Try a different search term or category.</p>
              </div>
            )}
          </div>
        )}

        <div className="mt-16 bg-brand-50 border border-brand-200 rounded-2xl p-8 text-center">
          <h3 className="text-xl font-bold mb-2">Still have questions?</h3>
          <p className="text-muted-foreground mb-6">Our team is happy to help you choose the right program.</p>
          <Link href="/contact" className="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold px-6 py-3 rounded-xl transition-colors">
            Contact Us <ArrowRight className="w-4 h-4" />
          </Link>
        </div>
      </div>
    </>
  );
}
