'use client';
import { useEffect, useState, useCallback } from 'react';
import { supabase } from '@/lib/supabase';
import { ProgramCard } from '@/components/shared/ProgramCard';
import { CardSkeleton } from '@/components/shared/Skeleton';
import { Search, SlidersHorizontal, X, ChevronDown } from 'lucide-react';
import { Button } from '@/components/ui/button';
import type { Program, ProgramCategory } from '@/lib/database.types';
import { cn } from '@/lib/utils';

const levels = [
  { value: '', label: 'All Levels' },
  { value: 'beginner', label: 'Beginner' },
  { value: 'intermediate', label: 'Intermediate' },
  { value: 'advanced', label: 'Advanced' },
];
const modes = [
  { value: '', label: 'All Formats' },
  { value: 'online', label: 'Online' },
  { value: 'in_person', label: 'In-Person' },
  { value: 'hybrid', label: 'Hybrid' },
];
const sorts = [
  { value: 'order_index', label: 'Featured' },
  { value: 'price_asc', label: 'Price: Low to High' },
  { value: 'price_desc', label: 'Price: High to Low' },
  { value: 'rating', label: 'Top Rated' },
  { value: 'enrollment_count', label: 'Most Popular' },
];

export default function ProgramsPage() {
  const [programs, setPrograms] = useState<Program[]>([]);
  const [categories, setCategories] = useState<ProgramCategory[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('');
  const [selectedLevel, setSelectedLevel] = useState('');
  const [selectedMode, setSelectedMode] = useState('');
  const [sortBy, setSortBy] = useState('order_index');
  const [filtersOpen, setFiltersOpen] = useState(false);

  useEffect(() => {
    supabase.from('program_categories').select('*').eq('is_active', true).order('order_index').then(({ data }) => {
      if (data) setCategories(data);
    });
  }, []);

  const loadPrograms = useCallback(async () => {
    setLoading(true);
    let query = supabase.from('programs').select('*').eq('is_published', true).eq('is_active', true);

    if (selectedCategory) query = query.eq('category_id', selectedCategory);
    if (selectedLevel) query = query.eq('level', selectedLevel);
    if (selectedMode) query = query.eq('delivery_mode', selectedMode);
    if (search) query = query.ilike('title', `%${search}%`);

    if (sortBy === 'price_asc') query = query.order('price', { ascending: true });
    else if (sortBy === 'price_desc') query = query.order('price', { ascending: false });
    else if (sortBy === 'rating') query = query.order('rating', { ascending: false });
    else if (sortBy === 'enrollment_count') query = query.order('enrollment_count', { ascending: false });
    else query = query.order('order_index');

    const { data } = await query;
    setPrograms(data || []);
    setLoading(false);
  }, [selectedCategory, selectedLevel, selectedMode, search, sortBy]);

  useEffect(() => {
    const t = setTimeout(loadPrograms, 300);
    return () => clearTimeout(t);
  }, [loadPrograms]);

  const clearFilters = () => {
    setSearch('');
    setSelectedCategory('');
    setSelectedLevel('');
    setSelectedMode('');
    setSortBy('order_index');
  };

  const hasFilters = search || selectedCategory || selectedLevel || selectedMode;

  return (
    <>
      {/* Page Header */}
      <div className="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <span className="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Training Programs</span>
          <h1 className="text-4xl sm:text-5xl font-black text-white mb-4">Learn Skills That Pay</h1>
          <p className="text-gray-400 text-lg max-w-2xl mx-auto">
            Choose from {programs.length > 0 ? programs.length + '+' : 'dozens of'} practical programs designed to help you earn money online.
          </p>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {/* Search & Filters */}
        <div className="bg-card border border-border rounded-2xl p-4 mb-8 space-y-4">
          <div className="flex gap-3">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
              <input
                type="text"
                placeholder="Search programs..."
                value={search}
                onChange={e => setSearch(e.target.value)}
                className="w-full pl-9 pr-4 py-2.5 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
              />
              {search && (
                <button onClick={() => setSearch('')} className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                  <X className="w-4 h-4" />
                </button>
              )}
            </div>
            <Button variant="outline" onClick={() => setFiltersOpen(!filtersOpen)} className="gap-2 whitespace-nowrap">
              <SlidersHorizontal className="w-4 h-4" /> Filters
              {hasFilters && <span className="bg-brand-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">!</span>}
            </Button>
          </div>

          {filtersOpen && (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2 border-t border-border">
              <div>
                <label className="text-xs font-medium text-muted-foreground mb-1.5 block">Category</label>
                <select
                  value={selectedCategory}
                  onChange={e => setSelectedCategory(e.target.value)}
                  className="w-full px-3 py-2 text-sm bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500"
                >
                  <option value="">All Categories</option>
                  {categories.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                </select>
              </div>
              <div>
                <label className="text-xs font-medium text-muted-foreground mb-1.5 block">Level</label>
                <select
                  value={selectedLevel}
                  onChange={e => setSelectedLevel(e.target.value)}
                  className="w-full px-3 py-2 text-sm bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500"
                >
                  {levels.map(l => <option key={l.value} value={l.value}>{l.label}</option>)}
                </select>
              </div>
              <div>
                <label className="text-xs font-medium text-muted-foreground mb-1.5 block">Format</label>
                <select
                  value={selectedMode}
                  onChange={e => setSelectedMode(e.target.value)}
                  className="w-full px-3 py-2 text-sm bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500"
                >
                  {modes.map(m => <option key={m.value} value={m.value}>{m.label}</option>)}
                </select>
              </div>
              <div>
                <label className="text-xs font-medium text-muted-foreground mb-1.5 block">Sort By</label>
                <select
                  value={sortBy}
                  onChange={e => setSortBy(e.target.value)}
                  className="w-full px-3 py-2 text-sm bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500"
                >
                  {sorts.map(s => <option key={s.value} value={s.value}>{s.label}</option>)}
                </select>
              </div>
            </div>
          )}

          {hasFilters && (
            <div className="flex items-center gap-2 pt-1">
              <span className="text-xs text-muted-foreground">{programs.length} results</span>
              <button onClick={clearFilters} className="text-xs text-brand-600 hover:text-brand-700 flex items-center gap-1 ml-auto">
                <X className="w-3 h-3" /> Clear all filters
              </button>
            </div>
          )}
        </div>

        {/* Category pills */}
        {categories.length > 0 && (
          <div className="flex gap-2 flex-wrap mb-8">
            <button
              onClick={() => setSelectedCategory('')}
              className={cn('text-sm font-medium px-4 py-2 rounded-full transition-colors', !selectedCategory ? 'bg-brand-500 text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80')}
            >
              All
            </button>
            {categories.map(cat => (
              <button
                key={cat.id}
                onClick={() => setSelectedCategory(selectedCategory === cat.id ? '' : cat.id)}
                className={cn('text-sm font-medium px-4 py-2 rounded-full transition-colors', selectedCategory === cat.id ? 'bg-brand-500 text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80')}
              >
                {cat.name}
              </button>
            ))}
          </div>
        )}

        {/* Programs grid */}
        <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {loading ? (
            Array.from({ length: 6 }).map((_, i) => <CardSkeleton key={i} />)
          ) : programs.length > 0 ? (
            programs.map(p => <ProgramCard key={p.id} program={p} />)
          ) : (
            <div className="col-span-3 text-center py-20">
              <Search className="w-12 h-12 text-muted-foreground/30 mx-auto mb-4" />
              <h3 className="text-lg font-semibold text-muted-foreground mb-2">No programs found</h3>
              <p className="text-sm text-muted-foreground mb-4">Try adjusting your filters or search term.</p>
              <Button variant="outline" onClick={clearFilters}>Clear Filters</Button>
            </div>
          )}
        </div>
      </div>
    </>
  );
}
