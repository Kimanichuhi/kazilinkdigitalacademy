'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { BarChart3, Users, TrendingUp, DollarSign } from 'lucide-react';
import { AreaChart, Area, BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';

const bookingMonthly = [
  { month: 'Jan', bookings: 24, revenue: 360000 },
  { month: 'Feb', bookings: 28, revenue: 420000 },
  { month: 'Mar', bookings: 35, revenue: 525000 },
  { month: 'Apr', bookings: 42, revenue: 630000 },
  { month: 'May', bookings: 38, revenue: 570000 },
  { month: 'Jun', bookings: 55, revenue: 825000 },
  { month: 'Jul', bookings: 67, revenue: 1005000 },
];

const programPopularity = [
  { name: 'Freelancing', value: 35, color: '#4CAF50' },
  { name: 'E-Commerce', value: 22, color: '#f97316' },
  { name: 'Digital Mktg', value: 20, color: '#10b981' },
  { name: 'Full-Stack Dev', value: 15, color: '#8b5cf6' },
  { name: 'Others', value: 8, color: '#6b7280' },
];

export default function AdminAnalyticsPage() {
  const [stats, setStats] = useState({ totalBookings: 0, totalRevenue: 0, avgValue: 0 });

  useEffect(() => {
    supabase.from('bookings').select('total_amount, amount_paid').then(({ data }) => {
      if (data) {
        const total = data.length;
        const revenue = data.reduce((s, b) => s + (b.amount_paid || 0), 0);
        setStats({ totalBookings: total, totalRevenue: revenue, avgValue: total > 0 ? Math.round(revenue / total) : 0 });
      }
    });
  }, []);

  return (
    <div className="p-6 space-y-6">
      <h1 className="text-2xl font-black">Analytics</h1>

      <div className="grid sm:grid-cols-3 gap-4">
        {[
          { label: 'Total Bookings', value: stats.totalBookings.toLocaleString(), icon: Users, color: 'text-brand-600', bg: 'bg-brand-50' },
          { label: 'Total Revenue', value: `KES ${(stats.totalRevenue / 1000).toFixed(0)}K`, icon: DollarSign, color: 'text-green-600', bg: 'bg-green-50' },
          { label: 'Avg Booking Value', value: `KES ${stats.avgValue.toLocaleString()}`, icon: TrendingUp, color: 'text-orange-600', bg: 'bg-orange-50' },
        ].map(s => {
          const Icon = s.icon;
          return (
            <div key={s.label} className="bg-card border border-border rounded-2xl p-5">
              <div className={`w-10 h-10 rounded-xl flex items-center justify-center mb-3 ${s.bg}`}>
                <Icon className={`w-5 h-5 ${s.color}`} />
              </div>
              <p className="text-2xl font-black">{s.value}</p>
              <p className="text-sm text-muted-foreground">{s.label}</p>
            </div>
          );
        })}
      </div>

      <div className="grid lg:grid-cols-2 gap-6">
        <div className="bg-card border border-border rounded-2xl p-5">
          <h2 className="font-bold mb-4">Monthly Bookings</h2>
          <ResponsiveContainer width="100%" height={220}>
            <AreaChart data={bookingMonthly}>
              <defs>
                <linearGradient id="aGrad" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="5%" stopColor="#4CAF50" stopOpacity={0.3} />
                  <stop offset="95%" stopColor="#4CAF50" stopOpacity={0} />
                </linearGradient>
              </defs>
              <CartesianGrid strokeDasharray="3 3" className="opacity-30" />
              <XAxis dataKey="month" tick={{ fontSize: 12 }} />
              <YAxis tick={{ fontSize: 12 }} />
              <Tooltip />
              <Area type="monotone" dataKey="bookings" stroke="#4CAF50" fill="url(#aGrad)" strokeWidth={2} />
            </AreaChart>
          </ResponsiveContainer>
        </div>

        <div className="bg-card border border-border rounded-2xl p-5">
          <h2 className="font-bold mb-4">Revenue Trend (KES)</h2>
          <ResponsiveContainer width="100%" height={220}>
            <BarChart data={bookingMonthly}>
              <CartesianGrid strokeDasharray="3 3" className="opacity-30" />
              <XAxis dataKey="month" tick={{ fontSize: 12 }} />
              <YAxis tick={{ fontSize: 12 }} tickFormatter={v => `${(v/1000).toFixed(0)}K`} />
              <Tooltip formatter={(v: number) => [`KES ${v.toLocaleString()}`, 'Revenue']} />
              <Bar dataKey="revenue" fill="#10b981" radius={[4, 4, 0, 0]} />
            </BarChart>
          </ResponsiveContainer>
        </div>

        <div className="bg-card border border-border rounded-2xl p-5">
          <h2 className="font-bold mb-4">Program Popularity</h2>
          <div className="flex items-center gap-6">
            <ResponsiveContainer width={160} height={160}>
              <PieChart>
                <Pie data={programPopularity} cx="50%" cy="50%" innerRadius={40} outerRadius={70} dataKey="value">
                  {programPopularity.map((entry, i) => <Cell key={i} fill={entry.color} />)}
                </Pie>
              </PieChart>
            </ResponsiveContainer>
            <div className="space-y-2 flex-1">
              {programPopularity.map(p => (
                <div key={p.name} className="flex items-center gap-2 text-sm">
                  <div className="w-3 h-3 rounded-full flex-shrink-0" style={{ backgroundColor: p.color }} />
                  <span className="flex-1 text-muted-foreground">{p.name}</span>
                  <span className="font-semibold">{p.value}%</span>
                </div>
              ))}
            </div>
          </div>
        </div>

        <div className="bg-card border border-border rounded-2xl p-5">
          <h2 className="font-bold mb-4">Key Metrics</h2>
          <div className="space-y-4">
            {[
              { label: 'Conversion Rate', value: '34%', desc: 'Visitors who book' },
              { label: 'Avg Time to Approve', value: '18h', desc: 'From booking to approval' },
              { label: 'Returning Students', value: '28%', desc: 'Enrolled in 2+ programs' },
              { label: 'Referral Rate', value: '42%', desc: 'Bookings from word of mouth' },
            ].map(m => (
              <div key={m.label} className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium">{m.label}</p>
                  <p className="text-xs text-muted-foreground">{m.desc}</p>
                </div>
                <span className="text-xl font-black text-brand-600">{m.value}</span>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
