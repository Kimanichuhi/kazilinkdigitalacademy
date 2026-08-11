'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { CohortCard } from '@/components/shared/CohortCard';
import { CardSkeleton } from '@/components/shared/Skeleton';
import { Calendar, List, Search, ChevronLeft, ChevronRight } from 'lucide-react';
import type { Cohort, Program, Trainer } from '@/lib/database.types';
import { format, startOfMonth, endOfMonth, eachDayOfInterval, isSameDay, isToday } from 'date-fns';
import { cn } from '@/lib/utils';

type CohortWithRelations = Cohort & { programs: Program; trainers: Trainer | null };

export default function CohortsPage() {
  const [cohorts, setCohorts] = useState<CohortWithRelations[]>([]);
  const [loading, setLoading] = useState(true);
  const [view, setView] = useState<'list' | 'calendar'>('list');
  const [search, setSearch] = useState('');
  const [currentMonth, setCurrentMonth] = useState(new Date());

  useEffect(() => {
    async function load() {
      const { data } = await supabase
        .from('cohorts')
        .select('*, programs(*), trainers(*)')
        .in('status', ['open', 'upcoming', 'in_progress'])
        .order('start_date');
      if (data) setCohorts(data as CohortWithRelations[]);
      setLoading(false);
    }
    load();
  }, []);

  const filtered = cohorts.filter(c =>
    !search || c.name.toLowerCase().includes(search.toLowerCase()) ||
    c.programs?.title?.toLowerCase().includes(search.toLowerCase())
  );

  const days = eachDayOfInterval({ start: startOfMonth(currentMonth), end: endOfMonth(currentMonth) });
  const getCohortsForDay = (day: Date) =>
    cohorts.filter(c => isSameDay(new Date(c.start_date), day));

  return (
    <>
      <div className="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <span className="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Schedule</span>
          <h1 className="text-4xl sm:text-5xl font-black text-white mb-4">Upcoming Cohorts</h1>
          <p className="text-gray-400 text-lg max-w-2xl mx-auto">Find the perfect cohort time and register your seat before it fills up.</p>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {/* Controls */}
        <div className="flex flex-col sm:flex-row gap-3 mb-8">
          <div className="relative flex-1 max-w-sm">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
            <input
              type="text"
              placeholder="Search cohorts..."
              value={search}
              onChange={e => setSearch(e.target.value)}
              className="w-full pl-9 pr-4 py-2.5 text-sm bg-card border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500"
            />
          </div>
          <div className="flex gap-1 bg-muted rounded-xl p-1">
            <button
              onClick={() => setView('list')}
              className={cn('flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors', view === 'list' ? 'bg-white text-foreground shadow-sm' : 'text-muted-foreground')}
            >
              <List className="w-4 h-4" /> List
            </button>
            <button
              onClick={() => setView('calendar')}
              className={cn('flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors', view === 'calendar' ? 'bg-white text-foreground shadow-sm' : 'text-muted-foreground')}
            >
              <Calendar className="w-4 h-4" /> Calendar
            </button>
          </div>
        </div>

        {view === 'list' ? (
          loading ? (
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
              {Array.from({ length: 6 }).map((_, i) => <CardSkeleton key={i} />)}
            </div>
          ) : filtered.length > 0 ? (
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
              {filtered.map(c => <CohortCard key={c.id} cohort={c} />)}
            </div>
          ) : (
            <div className="text-center py-20 text-muted-foreground">
              <Calendar className="w-12 h-12 mx-auto mb-4 opacity-30" />
              <p className="text-lg font-medium">No cohorts found</p>
              <p className="text-sm mt-1">Check back soon for upcoming intakes.</p>
            </div>
          )
        ) : (
          <div className="bg-card border border-border rounded-2xl overflow-hidden">
            {/* Calendar header */}
            <div className="flex items-center justify-between p-5 border-b border-border">
              <h2 className="text-lg font-bold">{format(currentMonth, 'MMMM yyyy')}</h2>
              <div className="flex gap-2">
                <button
                  onClick={() => setCurrentMonth(m => new Date(m.getFullYear(), m.getMonth() - 1))}
                  className="p-2 hover:bg-muted rounded-lg transition-colors"
                >
                  <ChevronLeft className="w-4 h-4" />
                </button>
                <button
                  onClick={() => setCurrentMonth(new Date())}
                  className="px-3 py-1 text-sm bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors"
                >
                  Today
                </button>
                <button
                  onClick={() => setCurrentMonth(m => new Date(m.getFullYear(), m.getMonth() + 1))}
                  className="p-2 hover:bg-muted rounded-lg transition-colors"
                >
                  <ChevronRight className="w-4 h-4" />
                </button>
              </div>
            </div>

            {/* Day headers */}
            <div className="grid grid-cols-7 border-b border-border">
              {['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map(d => (
                <div key={d} className="text-center text-xs font-medium text-muted-foreground py-2.5">{d}</div>
              ))}
            </div>

            {/* Calendar grid */}
            <div className="grid grid-cols-7">
              {/* Empty cells for first day offset */}
              {Array.from({ length: days[0].getDay() }).map((_, i) => (
                <div key={`empty-${i}`} className="border-b border-r border-border p-2 min-h-[80px]" />
              ))}
              {days.map(day => {
                const dayEvents = getCohortsForDay(day);
                return (
                  <div
                    key={day.toISOString()}
                    className={cn('border-b border-r border-border p-2 min-h-[80px]', isToday(day) ? 'bg-brand-50' : '')}
                  >
                    <span className={cn('text-sm font-medium', isToday(day) ? 'text-brand-600' : 'text-muted-foreground')}>
                      {format(day, 'd')}
                    </span>
                    {dayEvents.map(e => (
                      <div key={e.id} className="mt-1 text-[10px] bg-brand-500/20 text-brand-700 rounded px-1 py-0.5 truncate">
                        {e.programs?.title || e.name}
                      </div>
                    ))}
                  </div>
                );
              })}
            </div>
          </div>
        )}
      </div>
    </>
  );
}
