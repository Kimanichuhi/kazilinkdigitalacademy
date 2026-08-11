'use client';
import { useEffect, useState, useCallback } from 'react';
import { supabase } from '@/lib/supabase';
import { toast } from 'sonner';
import { Search, Filter, Eye, Check, X, Clock, Download } from 'lucide-react';
import type { Booking } from '@/lib/database.types';
import { format } from 'date-fns';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import Link from 'next/link';

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

const statuses = ['', 'draft', 'awaiting_payment', 'paid', 'pending_approval', 'approved', 'rejected', 'cancelled', 'completed'];

export default function AdminBookingsPage() {
  const [bookings, setBookings] = useState<Booking[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [status, setStatus] = useState('');
  const [page, setPage] = useState(0);
  const [total, setTotal] = useState(0);
  const pageSize = 20;

  const loadBookings = useCallback(async () => {
    setLoading(true);
    let query = supabase.from('bookings').select('*', { count: 'exact' });
    if (status) query = query.eq('status', status);
    if (search) query = query.or(`full_name.ilike.%${search}%,email.ilike.%${search}%,booking_number.ilike.%${search}%`);
    query = query.order('created_at', { ascending: false }).range(page * pageSize, (page + 1) * pageSize - 1);

    const { data, count } = await query;
    setBookings(data || []);
    setTotal(count || 0);
    setLoading(false);
  }, [status, search, page]);

  useEffect(() => { const t = setTimeout(loadBookings, 300); return () => clearTimeout(t); }, [loadBookings]);

  async function updateStatus(id: string, newStatus: string) {
    const updates: Partial<Booking> = {
      status: newStatus as Booking['status'],
      updated_at: new Date().toISOString(),
    };
    if (newStatus === 'approved') updates.approved_at = new Date().toISOString();

    const { error } = await supabase.from('bookings').update(updates).eq('id', id);
    if (error) { toast.error('Failed to update status'); return; }
    toast.success(`Booking ${newStatus}`);
    loadBookings();
  }

  const totalPages = Math.ceil(total / pageSize);

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-black">Bookings</h1>
          <p className="text-sm text-muted-foreground">{total} total bookings</p>
        </div>
        <Button variant="outline" size="sm" className="gap-2">
          <Download className="w-4 h-4" /> Export
        </Button>
      </div>

      {/* Filters */}
      <div className="bg-card border border-border rounded-2xl p-4 flex flex-col sm:flex-row gap-3">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
          <input
            value={search}
            onChange={e => { setSearch(e.target.value); setPage(0); }}
            placeholder="Search by name, email, booking number..."
            className="w-full pl-9 pr-4 py-2 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500"
          />
        </div>
        <select
          value={status}
          onChange={e => { setStatus(e.target.value); setPage(0); }}
          className="px-3 py-2 text-sm bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 capitalize"
        >
          {statuses.map(s => (
            <option key={s} value={s}>{s || 'All Statuses'}</option>
          ))}
        </select>
      </div>

      {/* Table */}
      <div className="bg-card border border-border rounded-2xl overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-border bg-muted/30">
                <th className="text-left px-5 py-3 font-medium text-muted-foreground">Booking #</th>
                <th className="text-left px-5 py-3 font-medium text-muted-foreground">Student</th>
                <th className="text-left px-5 py-3 font-medium text-muted-foreground hidden md:table-cell">Phone</th>
                <th className="text-left px-5 py-3 font-medium text-muted-foreground hidden lg:table-cell">Payment</th>
                <th className="text-left px-5 py-3 font-medium text-muted-foreground">Status</th>
                <th className="text-left px-5 py-3 font-medium text-muted-foreground hidden xl:table-cell">Date</th>
                <th className="text-left px-5 py-3 font-medium text-muted-foreground">Actions</th>
              </tr>
            </thead>
            <tbody>
              {loading ? (
                Array.from({ length: 8 }).map((_, i) => (
                  <tr key={i} className="border-b border-border">
                    {Array.from({ length: 7 }).map((_, j) => (
                      <td key={j} className="px-5 py-3"><div className="h-4 skeleton rounded w-16" /></td>
                    ))}
                  </tr>
                ))
              ) : bookings.length === 0 ? (
                <tr><td colSpan={7} className="px-5 py-12 text-center text-muted-foreground">No bookings found</td></tr>
              ) : bookings.map(b => (
                <tr key={b.id} className="border-b border-border hover:bg-muted/20 transition-colors">
                  <td className="px-5 py-3 font-mono text-xs text-brand-600 whitespace-nowrap">{b.booking_number}</td>
                  <td className="px-5 py-3">
                    <p className="font-medium whitespace-nowrap">{b.full_name}</p>
                    <p className="text-xs text-muted-foreground">{b.email}</p>
                  </td>
                  <td className="px-5 py-3 hidden md:table-cell text-muted-foreground">{b.phone}</td>
                  <td className="px-5 py-3 hidden lg:table-cell">
                    <div>
                      <p className="font-medium">{b.currency} {b.total_amount?.toLocaleString() || '—'}</p>
                      <p className="text-xs text-muted-foreground capitalize">{b.payment_method || '—'}</p>
                    </div>
                  </td>
                  <td className="px-5 py-3">
                    <span className={cn('text-xs font-medium px-2 py-0.5 rounded-full whitespace-nowrap capitalize', statusColors[b.status] || '')}>
                      {b.status.replace('_', ' ')}
                    </span>
                  </td>
                  <td className="px-5 py-3 hidden xl:table-cell text-xs text-muted-foreground whitespace-nowrap">
                    {format(new Date(b.created_at), 'dd MMM yyyy')}
                  </td>
                  <td className="px-5 py-3">
                    <div className="flex gap-1">
                      {b.status === 'pending_approval' && (
                        <>
                          <button
                            onClick={() => updateStatus(b.id, 'approved')}
                            className="p-1.5 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg transition-colors"
                            title="Approve"
                          >
                            <Check className="w-3.5 h-3.5" />
                          </button>
                          <button
                            onClick={() => updateStatus(b.id, 'rejected')}
                            className="p-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors"
                            title="Reject"
                          >
                            <X className="w-3.5 h-3.5" />
                          </button>
                        </>
                      )}
                      {b.status === 'paid' && (
                        <button
                          onClick={() => updateStatus(b.id, 'pending_approval')}
                          className="p-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 rounded-lg transition-colors"
                          title="Move to Pending Approval"
                        >
                          <Clock className="w-3.5 h-3.5" />
                        </button>
                      )}
                      <Link
                        href={`/admin/bookings/${b.id}`}
                        className="p-1.5 bg-brand-100 hover:bg-brand-200 text-brand-700 rounded-lg transition-colors"
                        title="View Details"
                      >
                        <Eye className="w-3.5 h-3.5" />
                      </Link>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {totalPages > 1 && (
          <div className="flex items-center justify-between px-5 py-3 border-t border-border">
            <p className="text-sm text-muted-foreground">
              Showing {page * pageSize + 1}–{Math.min((page + 1) * pageSize, total)} of {total}
            </p>
            <div className="flex gap-2">
              <Button variant="outline" size="sm" disabled={page === 0} onClick={() => setPage(p => p - 1)}>Previous</Button>
              <Button variant="outline" size="sm" disabled={page >= totalPages - 1} onClick={() => setPage(p => p + 1)}>Next</Button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
