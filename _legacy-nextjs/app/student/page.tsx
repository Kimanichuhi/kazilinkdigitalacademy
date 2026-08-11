'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { useAuth } from '@/components/providers/AuthProvider';
import Link from 'next/link';
import { BookOpen, ArrowRight, Award, Clock, CheckCircle, AlertCircle } from 'lucide-react';
import type { Booking, Program } from '@/lib/database.types';
import { format } from 'date-fns';
import { cn } from '@/lib/utils';

const statusConfig: Record<string, { label: string; color: string; icon: React.ReactNode }> = {
  draft: { label: 'Draft', color: 'bg-gray-100 text-gray-600', icon: <Clock className="w-3.5 h-3.5" /> },
  awaiting_payment: { label: 'Awaiting Payment', color: 'bg-yellow-100 text-yellow-700', icon: <AlertCircle className="w-3.5 h-3.5" /> },
  paid: { label: 'Payment Received', color: 'bg-blue-100 text-blue-700', icon: <CheckCircle className="w-3.5 h-3.5" /> },
  pending_approval: { label: 'Pending Approval', color: 'bg-orange-100 text-orange-700', icon: <Clock className="w-3.5 h-3.5" /> },
  approved: { label: 'Approved', color: 'bg-green-100 text-green-700', icon: <CheckCircle className="w-3.5 h-3.5" /> },
  rejected: { label: 'Rejected', color: 'bg-red-100 text-red-700', icon: <AlertCircle className="w-3.5 h-3.5" /> },
  cancelled: { label: 'Cancelled', color: 'bg-gray-100 text-gray-500', icon: <AlertCircle className="w-3.5 h-3.5" /> },
  completed: { label: 'Completed', color: 'bg-emerald-100 text-emerald-700', icon: <Award className="w-3.5 h-3.5" /> },
};

export default function StudentDashboard() {
  const { user, profile } = useAuth();
  const [bookings, setBookings] = useState<(Booking & { programs: Program })[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!user) return;
    supabase.from('bookings').select('*, programs(*)').eq('user_id', user.id).order('created_at', { ascending: false }).then(({ data }) => {
      if (data) setBookings(data as (Booking & { programs: Program })[]);
      setLoading(false);
    });
  }, [user]);

  const activeBookings = bookings.filter(b => ['approved', 'pending_approval', 'paid', 'awaiting_payment'].includes(b.status));
  const completedBookings = bookings.filter(b => b.status === 'completed');

  return (
    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      {/* Header */}
      <div className="mb-8">
        <h1 className="text-3xl font-black">My Dashboard</h1>
        <p className="text-muted-foreground mt-1">Welcome back, {profile?.full_name || user?.email?.split('@')[0]}</p>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        {[
          { label: 'Total Bookings', value: bookings.length, color: 'text-brand-600', bg: 'bg-brand-50' },
          { label: 'Active Programs', value: activeBookings.length, color: 'text-green-600', bg: 'bg-green-50' },
          { label: 'Completed', value: completedBookings.length, color: 'text-emerald-600', bg: 'bg-emerald-50' },
          { label: 'Certificates', value: completedBookings.length, color: 'text-orange-600', bg: 'bg-orange-50' },
        ].map(s => (
          <div key={s.label} className={`rounded-2xl p-5 border border-border bg-card`}>
            <p className={`text-3xl font-black ${s.color}`}>{s.value}</p>
            <p className="text-sm text-muted-foreground mt-0.5">{s.label}</p>
          </div>
        ))}
      </div>

      <div className="grid lg:grid-cols-3 gap-6">
        {/* Bookings */}
        <div className="lg:col-span-2 space-y-4">
          <div className="flex items-center justify-between mb-2">
            <h2 className="text-xl font-bold">My Bookings</h2>
            <Link href="/booking" className="text-sm text-brand-600 hover:underline flex items-center gap-1">
              + New Booking <ArrowRight className="w-3.5 h-3.5" />
            </Link>
          </div>

          {loading ? (
            Array.from({ length: 3 }).map((_, i) => <div key={i} className="h-28 skeleton rounded-2xl" />)
          ) : bookings.length === 0 ? (
            <div className="bg-card border border-border rounded-2xl p-10 text-center">
              <BookOpen className="w-12 h-12 text-muted-foreground/30 mx-auto mb-4" />
              <h3 className="font-semibold text-muted-foreground mb-2">No bookings yet</h3>
              <p className="text-sm text-muted-foreground mb-4">Explore our programs and book your first training.</p>
              <Link href="/programs" className="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                Browse Programs <ArrowRight className="w-3.5 h-3.5" />
              </Link>
            </div>
          ) : bookings.map(b => {
            const status = statusConfig[b.status] || statusConfig.draft;
            return (
              <div key={b.id} className="bg-card border border-border rounded-2xl p-5">
                <div className="flex items-start justify-between gap-3 mb-3">
                  <div className="flex-1 min-w-0">
                    <h3 className="font-bold truncate">{b.programs?.title || 'Program'}</h3>
                    <p className="text-xs text-muted-foreground mt-0.5 font-mono">{b.booking_number}</p>
                  </div>
                  <span className={cn('flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full flex-shrink-0', status.color)}>
                    {status.icon} {status.label}
                  </span>
                </div>

                <div className="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs text-muted-foreground">
                  <div><span className="font-medium text-foreground">Booked: </span>{format(new Date(b.created_at), 'dd MMM yyyy')}</div>
                  <div><span className="font-medium text-foreground">Amount: </span>{b.currency} {b.total_amount?.toLocaleString() || '—'}</div>
                  <div><span className="font-medium text-foreground">Payment: </span><span className="capitalize">{b.payment_status}</span></div>
                </div>

                {b.status === 'awaiting_payment' && (
                  <div className="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-xl text-xs text-yellow-800">
                    <AlertCircle className="w-3.5 h-3.5 inline mr-1" />
                    Payment pending. Please complete your M-Pesa or bank transfer to proceed.
                  </div>
                )}

                {b.rejection_reason && (
                  <div className="mt-3 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-800">
                    <AlertCircle className="w-3.5 h-3.5 inline mr-1" />
                    {b.rejection_reason}
                  </div>
                )}

                {b.status === 'completed' && (
                  <div className="mt-3">
                    <button className="flex items-center gap-2 text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                      <Award className="w-3.5 h-3.5" /> Download Certificate
                    </button>
                  </div>
                )}
              </div>
            );
          })}
        </div>

        {/* Sidebar */}
        <div className="space-y-5">
          {/* Profile card */}
          <div className="bg-card border border-border rounded-2xl p-5">
            <h2 className="font-bold mb-4">My Profile</h2>
            <div className="flex flex-col items-center text-center mb-4">
              <div className="w-16 h-16 rounded-full bg-brand-500 flex items-center justify-center text-white text-2xl font-bold mb-3">
                {(profile?.full_name || user?.email || 'U')[0].toUpperCase()}
              </div>
              <p className="font-bold">{profile?.full_name || 'No name set'}</p>
              <p className="text-sm text-muted-foreground">{user?.email}</p>
              {profile?.phone && <p className="text-xs text-muted-foreground">{profile.phone}</p>}
            </div>
            <Link href="/student/profile" className="block w-full text-center border border-border hover:border-brand-500 hover:text-brand-600 text-sm font-medium py-2 rounded-xl transition-colors">
              Edit Profile
            </Link>
          </div>

          {/* Quick links */}
          <div className="bg-card border border-border rounded-2xl p-5">
            <h2 className="font-bold mb-3">Quick Links</h2>
            <div className="space-y-1">
              {[
                { label: 'Browse Programs', href: '/programs' },
                { label: 'Book New Training', href: '/booking' },
                { label: 'Download Resources', href: '/resources' },
                { label: 'Read Blog', href: '/blog' },
                { label: 'Contact Support', href: '/contact' },
              ].map(l => (
                <Link key={l.href} href={l.href} className="flex items-center justify-between px-3 py-2 text-sm rounded-lg hover:bg-muted transition-colors text-muted-foreground hover:text-foreground">
                  {l.label}
                  <ArrowRight className="w-3.5 h-3.5" />
                </Link>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
