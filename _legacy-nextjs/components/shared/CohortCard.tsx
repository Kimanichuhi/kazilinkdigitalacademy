'use client';
import Link from 'next/link';
import { Calendar, Clock, MapPin, Users, ArrowRight, Wifi } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import type { Cohort, Program, Trainer } from '@/lib/database.types';
import { format } from 'date-fns';

interface CohortCardProps {
  cohort: Cohort & { programs?: Program; trainers?: Trainer | null };
  className?: string;
}

const statusConfig: Record<string, { label: string; className: string }> = {
  upcoming: { label: 'Upcoming', className: 'bg-blue-100 text-blue-700' },
  open: { label: 'Open for Registration', className: 'bg-green-100 text-green-700' },
  full: { label: 'Fully Booked', className: 'bg-red-100 text-red-700' },
  in_progress: { label: 'In Progress', className: 'bg-orange-100 text-orange-700' },
  completed: { label: 'Completed', className: 'bg-gray-100 text-gray-700' },
  cancelled: { label: 'Cancelled', className: 'bg-gray-100 text-gray-500' },
};

export function CohortCard({ cohort, className }: CohortCardProps) {
  const status = statusConfig[cohort.status] || statusConfig.upcoming;
  const seatsLeft = cohort.total_seats - cohort.booked_seats;
  const fillPercent = cohort.total_seats > 0 ? Math.round((cohort.booked_seats / cohort.total_seats) * 100) : 0;

  return (
    <div className={cn('bg-card border border-border rounded-2xl p-5 card-hover flex flex-col gap-4', className)}>
      <div className="flex items-start justify-between gap-3">
        <div className="flex-1 min-w-0">
          <h3 className="font-bold text-foreground text-base line-clamp-2 mb-1">{cohort.name}</h3>
          {cohort.programs && (
            <p className="text-sm text-brand-600 font-medium">{cohort.programs.title}</p>
          )}
        </div>
        <span className={cn('text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap flex-shrink-0', status.className)}>
          {status.label}
        </span>
      </div>

      <div className="space-y-2 text-sm text-muted-foreground">
        <div className="flex items-center gap-2">
          <Calendar className="w-4 h-4 flex-shrink-0 text-brand-500" />
          <span>
            {cohort.start_date ? format(new Date(cohort.start_date), 'dd MMM yyyy') : 'TBD'}
            {cohort.end_date && ` – ${format(new Date(cohort.end_date), 'dd MMM yyyy')}`}
          </span>
        </div>
        {cohort.schedule_details && (
          <div className="flex items-center gap-2">
            <Clock className="w-4 h-4 flex-shrink-0 text-brand-500" />
            <span>{cohort.schedule_details}</span>
          </div>
        )}
        {cohort.venue ? (
          <div className="flex items-center gap-2">
            <MapPin className="w-4 h-4 flex-shrink-0 text-brand-500" />
            <span>{cohort.venue}</span>
          </div>
        ) : cohort.online_platform ? (
          <div className="flex items-center gap-2">
            <Wifi className="w-4 h-4 flex-shrink-0 text-brand-500" />
            <span>Online via {cohort.online_platform}</span>
          </div>
        ) : null}
        {cohort.trainers && (
          <div className="flex items-center gap-2">
            {cohort.trainers.avatar_url ? (
              <img
                src={cohort.trainers.avatar_url}
                alt={cohort.trainers.full_name}
                className="w-5 h-5 rounded-full object-cover border border-border"
              />
            ) : (
              <div className="w-5 h-5 rounded-full bg-brand-500 flex items-center justify-center text-white text-[10px] font-bold">
                {cohort.trainers.full_name[0] || '?'}
              </div>
            )}
            <span>Trainer: <span className="font-medium text-foreground">{cohort.trainers.full_name}</span></span>
          </div>
        )}
      </div>

      {/* Seats progress */}
      <div>
        <div className="flex justify-between text-xs mb-1.5">
          <span className="text-muted-foreground"><Users className="w-3.5 h-3.5 inline mr-1" />{cohort.booked_seats}/{cohort.total_seats} seats</span>
          <span className={cn('font-semibold', seatsLeft <= 5 ? 'text-red-500' : 'text-green-600')}>
            {seatsLeft > 0 ? `${seatsLeft} seats left` : 'Fully booked'}
          </span>
        </div>
        <div className="h-1.5 bg-muted rounded-full overflow-hidden">
          <div
            className={cn('h-full rounded-full transition-all', fillPercent >= 90 ? 'bg-red-500' : fillPercent >= 60 ? 'bg-orange-500' : 'bg-green-500')}
            style={{ width: `${fillPercent}%` }}
          />
        </div>
      </div>

      <div className="flex items-center justify-between pt-1 border-t border-border">
        <div>
          {cohort.price ? (
            <span className="text-lg font-bold">{cohort.currency} {cohort.price.toLocaleString()}</span>
          ) : cohort.programs?.price ? (
            <span className="text-lg font-bold">{cohort.currency} {cohort.programs.price.toLocaleString()}</span>
          ) : null}
        </div>
        {cohort.status === 'open' || cohort.status === 'upcoming' ? (
          <Link
            href={`/booking?cohort=${cohort.id}`}
            className="flex items-center gap-1 bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors"
          >
            Book Now <ArrowRight className="w-3.5 h-3.5" />
          </Link>
        ) : (
          <span className="text-sm text-muted-foreground">Not available</span>
        )}
      </div>
    </div>
  );
}
