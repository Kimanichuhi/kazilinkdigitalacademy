'use client';
import { Star, Quote } from 'lucide-react';
import type { Testimonial } from '@/lib/database.types';
import { cn } from '@/lib/utils';

interface TestimonialCardProps {
  testimonial: Testimonial;
  className?: string;
}

export function TestimonialCard({ testimonial, className }: TestimonialCardProps) {
  return (
    <div className={cn('bg-card border border-border rounded-2xl p-6 flex flex-col gap-4', className)}>
      <div className="flex items-start justify-between">
        <Quote className="w-8 h-8 text-brand-500/30" />
        {testimonial.rating && (
          <div className="flex gap-0.5">
            {Array.from({ length: testimonial.rating }).map((_, i) => (
              <Star key={i} className="w-4 h-4 fill-amber-400 text-amber-400" />
            ))}
          </div>
        )}
      </div>

      <p className="text-sm text-muted-foreground leading-relaxed line-clamp-4 flex-1">
        "{testimonial.content}"
      </p>

      {(testimonial.income_before || testimonial.income_after) && (
        <div className="flex gap-3 p-3 bg-green-50 rounded-xl border border-green-100">
          {testimonial.income_before && (
            <div className="text-center flex-1">
              <p className="text-xs text-muted-foreground">Before</p>
              <p className="text-sm font-semibold text-red-600">{testimonial.income_before}</p>
            </div>
          )}
          {testimonial.income_before && testimonial.income_after && (
            <div className="w-px bg-border" />
          )}
          {testimonial.income_after && (
            <div className="text-center flex-1">
              <p className="text-xs text-muted-foreground">After</p>
              <p className="text-sm font-semibold text-green-600">{testimonial.income_after}</p>
            </div>
          )}
        </div>
      )}

      <div className="flex items-center gap-3 pt-2 border-t border-border">
        {testimonial.student_avatar_url ? (
          <img
            src={testimonial.student_avatar_url}
            alt={testimonial.student_name}
            className="w-10 h-10 rounded-full object-cover"
          />
        ) : (
          <div className="w-10 h-10 rounded-full bg-brand-500 flex items-center justify-center text-white font-bold text-sm">
            {testimonial.student_name[0]}
          </div>
        )}
        <div>
          <p className="text-sm font-semibold">{testimonial.student_name}</p>
          {testimonial.student_title && (
            <p className="text-xs text-muted-foreground">{testimonial.student_title}</p>
          )}
        </div>
      </div>
    </div>
  );
}
