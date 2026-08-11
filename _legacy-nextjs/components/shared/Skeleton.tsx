'use client';
import { cn } from '@/lib/utils';

interface SkeletonProps {
  className?: string;
}

export function Skeleton({ className }: SkeletonProps) {
  return <div className={cn('skeleton rounded-lg', className)} />;
}

export function CardSkeleton() {
  return (
    <div className="bg-card border border-border rounded-2xl overflow-hidden p-0">
      <Skeleton className="h-48 w-full rounded-none" />
      <div className="p-5 space-y-3">
        <Skeleton className="h-4 w-2/3" />
        <Skeleton className="h-3 w-full" />
        <Skeleton className="h-3 w-4/5" />
        <div className="flex justify-between pt-2">
          <Skeleton className="h-6 w-24" />
          <Skeleton className="h-8 w-24 rounded-lg" />
        </div>
      </div>
    </div>
  );
}

export function PageHeaderSkeleton() {
  return (
    <div className="py-16 space-y-4 text-center">
      <Skeleton className="h-6 w-32 mx-auto rounded-full" />
      <Skeleton className="h-10 w-96 mx-auto" />
      <Skeleton className="h-5 w-64 mx-auto" />
    </div>
  );
}
