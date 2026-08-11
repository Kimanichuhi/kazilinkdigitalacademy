'use client';
import Link from 'next/link';
import { Star, Clock, Users, TrendingUp, ArrowRight } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';
import type { Program } from '@/lib/database.types';

interface ProgramCardProps {
  program: Program;
  className?: string;
}

export function ProgramCard({ program, className }: ProgramCardProps) {
  const levelColors: Record<string, string> = {
    beginner: 'bg-green-100 text-green-700',
    intermediate: 'bg-blue-100 text-blue-700',
    advanced: 'bg-orange-100 text-orange-700',
    all_levels: 'bg-gray-100 text-gray-700',
  };

  const levelLabels: Record<string, string> = {
    beginner: 'Beginner',
    intermediate: 'Intermediate',
    advanced: 'Advanced',
    all_levels: 'All Levels',
  };

  return (
    <Link href={`/programs/${program.slug}`} className={cn('group block', className)}>
      <div className="bg-card border border-border rounded-2xl overflow-hidden card-hover h-full flex flex-col">
        {/* Thumbnail */}
        <div className="relative overflow-hidden">
          <div className="aspect-[16/9] bg-muted overflow-hidden">
            {program.thumbnail_url ? (
              <img
                src={program.thumbnail_url}
                alt={program.title}
                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
              />
            ) : (
              <div className="w-full h-full bg-gradient-to-br from-brand-400 to-navy-600 flex items-center justify-center">
                <TrendingUp className="w-12 h-12 text-white/50" />
              </div>
            )}
          </div>
          {program.is_featured && (
            <div className="absolute top-3 left-3">
              <span className="bg-orange-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                Featured
              </span>
            </div>
          )}
          {program.original_price && program.price && program.original_price > program.price && (
            <div className="absolute top-3 right-3">
              <span className="bg-green-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                {Math.round((1 - program.price / program.original_price) * 100)}% OFF
              </span>
            </div>
          )}
        </div>

        {/* Content */}
        <div className="p-5 flex flex-col flex-1">
          <div className="flex items-center gap-2 mb-3">
            <span className={cn('text-xs font-medium px-2.5 py-1 rounded-full', levelColors[program.level] || levelColors.beginner)}>
              {levelLabels[program.level] || program.level}
            </span>
            <span className="text-xs text-muted-foreground capitalize">{program.delivery_mode?.replace('_', ' ')}</span>
          </div>

          <h3 className="font-bold text-foreground text-base mb-2 line-clamp-2 group-hover:text-brand-600 transition-colors">
            {program.title}
          </h3>

          {program.short_description && (
            <p className="text-sm text-muted-foreground line-clamp-2 mb-4 flex-1">
              {program.short_description}
            </p>
          )}

          <div className="flex items-center gap-4 text-xs text-muted-foreground mb-4">
            {program.duration_label && (
              <span className="flex items-center gap-1">
                <Clock className="w-3.5 h-3.5" />{program.duration_label}
              </span>
            )}
            {program.enrollment_count > 0 && (
              <span className="flex items-center gap-1">
                <Users className="w-3.5 h-3.5" />{program.enrollment_count.toLocaleString()} enrolled
              </span>
            )}
            {program.rating > 0 && (
              <span className="flex items-center gap-1 text-amber-500">
                <Star className="w-3.5 h-3.5 fill-current" />
                {program.rating.toFixed(1)}
                <span className="text-muted-foreground">({program.review_count})</span>
              </span>
            )}
          </div>

          <div className="flex items-center justify-between pt-3 border-t border-border">
            <div>
              {program.price ? (
                <div className="flex items-baseline gap-2">
                  <span className="text-lg font-bold text-foreground">
                    {program.currency} {program.price.toLocaleString()}
                  </span>
                  {program.original_price && program.original_price > program.price && (
                    <span className="text-sm text-muted-foreground line-through">
                      {program.original_price.toLocaleString()}
                    </span>
                  )}
                </div>
              ) : (
                <span className="text-lg font-bold text-green-600">Free</span>
              )}
            </div>
            <span className="flex items-center gap-1 text-brand-600 text-sm font-medium group-hover:gap-2 transition-all">
              Enroll <ArrowRight className="w-3.5 h-3.5" />
            </span>
          </div>
        </div>
      </div>
    </Link>
  );
}
