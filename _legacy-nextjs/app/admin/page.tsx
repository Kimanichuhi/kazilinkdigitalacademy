'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import Link from 'next/link';
import {
  BookOpen, Users, Calendar, TrendingUp, Clock, CheckCircle,
  XCircle, AlertCircle, ArrowRight, DollarSign, Eye
} from 'lucide-react';
import type { Booking } from '@/lib/database.types';
import { format } from 'date-fns';
import { cn } from '@/lib/utils';
import { AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';

const statusColors: Record<string, string> = {
  draft: 'bg-gray-100 text-gray-600',
  awaiting_payment: 'bg-yellow-100 text-yellow-700',
  paid: 'bg-blue-100 text-blue-700',
  pending_approval: 'bg-orange-100 text-orange-700',
  approved: 'bg-green-100 text-green-700',
  rejected: 'bg-red-100 text-red-700',
  cancelled: 'bg-gray-100 text-gray-500',
  completed: 'bg-emerald-100 text-emerald-700',
};

export default function AdminDashboard() {
  const [stats, setStats] = useState({ bookings: 0, students: 0, revenue: 0, pending: 0 });
  const [recentBookings, setRecentBookings] = useState<Booking[]>([]);
  const [chartData, setChartData] = useState<{ month: string; bookings: number; revenue: number }[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function loadDashboard() {
      const [bookingsRes, pendingRes] = await Promise.all([
        supabase.from('bookings').select('*').order('created_at', { ascending: false }).limit(10),
        supabase.from('bookings').select('id', { count: 'exact' }).eq('status', 'pending_approval'),
      ]);

      const bookings = bookingsRes.data || [];
      setRecentBookings(bookings.slice(0, 8));

      const totalRevenue = bookings.reduce((sum, b) => sum + (b.amount_paid || 0), 0);
      const uniqueEmails = new Set(bookings.map(b => b.email)).size;

      setStats({
        bookings: bookingsRes.count || bookings.length,
        students: uniqueEmails,
        revenue: totalRevenue,
        pending: pendingRes.count || 0,
      });

      // Simulate chart data
      setChartData([
        { month: 'Feb', bookings: 28, revenue: 420000 },
        { month: 'Mar', bookings: 35, revenue: 525000 },
        { month: 'Apr', bookings: 42, revenue: 630000 },
        { month: 'May', bookings: 38, revenue: 570000 },
        { month: 'Jun', bookings: 55, revenue: 825000 },
        { month: 'Jul', bookings: 67, revenue: 1005000 },
      ]);
      setLoading(false);
    }
    loadDashboard();
  }, []);

  const statCards = [
    { label: 'Total Bookings', value: stats.bookings.toLocaleString(), icon: BookOpen, color: 'text-brand-600', bg: 'bg-brand-50', href: '/admin/bookings' },
    { label: 'Active Students', value: stats.students.toLocaleString(), icon: Users, color: 'text-green-600', bg: 'bg-green-50', href: '/admin/students' },
    { label: 'Revenue (KES)', value: `${(stats.revenue / 1000).toFixed(0)}K`, icon: DollarSign, color: 'text-orange-600', bg: 'bg-orange-50', href: '/admin/analytics' },
    { label: 'Pending Approval', value: stats.pending.toLocaleString(), icon: AlertCircle, color: 'text-amber-600', bg: 'bg-amber-50', href: '/admin/bookings' },
  ];

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-black">Dashboard</h1>
          <p className="text-muted-foreground text-sm mt-0.5">
            {format(new Date(), 'EEEE, dd MMMM yyyy')}
          </p>
        </div>
        <Link href="/admin/bookings?status=pending_approval" className="flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
          <AlertCircle className="w-4 h-4" />
          {stats.pending} Pending
        </Link>
      </div>

      {/* Stat cards */}
      <div className="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
        {statCards.map(s => {
          const Icon = s.icon;
          return (
            <Link key={s.label} href={s.href} className="group">
              <div className="bg-card border border-border rounded-2xl p-5 hover:shadow-md transition-all">
                <div className="flex items-center justify-between mb-3">
                  <div className={cn('w-10 h-10 rounded-xl flex items-center justify-center', s.bg)}>
                    <Icon className={cn('w-5 h-5', s.color)} />
                  </div>
                  <ArrowRight className="w-4 h-4 text-muted-foreground opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all" />
                </div>
                <p className="text-2xl font-black">{s.value}</p>
                <p className="text-sm text-muted-foreground mt-0.5">{s.label}</p>
              </div>
            </Link>
          );
        })}
      </div>

      {/* Charts + Quick Actions */}
      <div className="grid lg:grid-cols-3 gap-6">
        {/* Bookings Chart */}
        <div className="lg:col-span-2 bg-card border border-border rounded-2xl p-5">
          <div className="flex items-center justify-between mb-5">
            <h2 className="font-bold">Bookings Overview</h2>
            <span className="text-xs text-muted-foreground">Last 6 months</span>
          </div>
          <ResponsiveContainer width="100%" height={200}>
            <AreaChart data={chartData}>
              <defs>
                <linearGradient id="bookingsGrad" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="5%" stopColor="#4CAF50" stopOpacity={0.3} />
                  <stop offset="95%" stopColor="#4CAF50" stopOpacity={0} />
                </linearGradient>
              </defs>
              <CartesianGrid strokeDasharray="3 3" className="opacity-30" />
              <XAxis dataKey="month" tick={{ fontSize: 12 }} />
              <YAxis tick={{ fontSize: 12 }} />
              <Tooltip />
              <Area type="monotone" dataKey="bookings" stroke="#4CAF50" fill="url(#bookingsGrad)" strokeWidth={2} />
            </AreaChart>
          </ResponsiveContainer>
        </div>

        {/* Quick Actions */}
        <div className="bg-card border border-border rounded-2xl p-5">
          <h2 className="font-bold mb-4">Quick Actions</h2>
          <div className="space-y-2">
            {[
              { label: 'Review Pending Bookings', href: '/admin/bookings?status=pending_approval', color: 'text-amber-600 bg-amber-50', icon: Clock },
              { label: 'Add New Program', href: '/admin/programs', color: 'text-brand-600 bg-brand-50', icon: BookOpen },
              { label: 'Create New Cohort', href: '/admin/cohorts', color: 'text-green-600 bg-green-50', icon: Calendar },
              { label: 'Publish Blog Post', href: '/admin/blog', color: 'text-orange-600 bg-orange-50', icon: TrendingUp },
              { label: 'Manage Advertisements', href: '/admin/advertisements', color: 'text-purple-600 bg-purple-50', icon: Eye },
            ].map(a => {
              const Icon = a.icon;
              return (
                <Link key={a.href} href={a.href} className="flex items-center gap-3 p-3 rounded-xl hover:bg-muted/50 transition-colors group">
                  <div className={cn('w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0', a.color)}>
                    <Icon className="w-4 h-4" />
                  </div>
                  <span className="text-sm font-medium group-hover:text-brand-600 transition-colors">{a.label}</span>
                  <ArrowRight className="w-3.5 h-3.5 ml-auto text-muted-foreground opacity-0 group-hover:opacity-100 transition-opacity" />
                </Link>
              );
            })}
          </div>
        </div>
      </div>

      {/* Recent Bookings */}
      <div className="bg-card border border-border rounded-2xl overflow-hidden">
        <div className="flex items-center justify-between p-5 border-b border-border">
          <h2 className="font-bold">Recent Bookings</h2>
          <Link href="/admin/bookings" className="text-sm text-brand-600 hover:underline flex items-center gap-1">
            View all <ArrowRight className="w-3.5 h-3.5" />
          </Link>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-border bg-muted/30">
                <th className="text-left px-5 py-3 font-medium text-muted-foreground">Booking #</th>
                <th className="text-left px-5 py-3 font-medium text-muted-foreground">Student</th>
                <th className="text-left px-5 py-3 font-medium text-muted-foreground hidden md:table-cell">Program</th>
                <th className="text-left px-5 py-3 font-medium text-muted-foreground">Status</th>
                <th className="text-left px-5 py-3 font-medium text-muted-foreground hidden lg:table-cell">Date</th>
                <th className="text-left px-5 py-3 font-medium text-muted-foreground">Actions</th>
              </tr>
            </thead>
            <tbody>
              {loading ? (
                Array.from({ length: 5 }).map((_, i) => (
                  <tr key={i} className="border-b border-border">
                    {Array.from({ length: 6 }).map((_, j) => (
                      <td key={j} className="px-5 py-3"><div className="h-4 skeleton rounded w-20" /></td>
                    ))}
                  </tr>
                ))
              ) : recentBookings.map(booking => (
                <tr key={booking.id} className="border-b border-border hover:bg-muted/30 transition-colors">
                  <td className="px-5 py-3 font-mono text-xs text-brand-600">{booking.booking_number}</td>
                  <td className="px-5 py-3">
                    <div>
                      <p className="font-medium">{booking.full_name}</p>
                      <p className="text-xs text-muted-foreground">{booking.email}</p>
                    </div>
                  </td>
                  <td className="px-5 py-3 hidden md:table-cell text-xs text-muted-foreground">{booking.program_id?.slice(0, 8)}...</td>
                  <td className="px-5 py-3">
                    <span className={cn('text-xs font-medium px-2 py-0.5 rounded-full capitalize', statusColors[booking.status] || 'bg-gray-100 text-gray-600')}>
                      {booking.status.replace('_', ' ')}
                    </span>
                  </td>
                  <td className="px-5 py-3 hidden lg:table-cell text-xs text-muted-foreground">
                    {format(new Date(booking.created_at), 'dd MMM yyyy')}
                  </td>
                  <td className="px-5 py-3">
                    <Link href={`/admin/bookings/${booking.id}`} className="text-xs text-brand-600 hover:underline">
                      View
                    </Link>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
