'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { Search, Users, Mail, Phone } from 'lucide-react';
import type { Profile } from '@/lib/database.types';
import { format } from 'date-fns';
import { cn } from '@/lib/utils';

const roleColors: Record<string, string> = {
  student: 'bg-blue-100 text-blue-700',
  trainer: 'bg-green-100 text-green-700',
  admin: 'bg-orange-100 text-orange-700',
  super_admin: 'bg-red-100 text-red-700',
  content_manager: 'bg-purple-100 text-purple-700',
  marketing: 'bg-pink-100 text-pink-700',
  finance: 'bg-yellow-100 text-yellow-700',
  support: 'bg-teal-100 text-teal-700',
};

export default function AdminStudentsPage() {
  const [students, setStudents] = useState<Profile[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [total, setTotal] = useState(0);

  useEffect(() => {
    const t = setTimeout(async () => {
      let q = supabase.from('profiles').select('*', { count: 'exact' }).eq('role', 'student');
      if (search) q = q.or(`full_name.ilike.%${search}%,email.ilike.%${search}%`);
      q = q.order('created_at', { ascending: false }).limit(50);
      const { data, count } = await q;
      if (data) setStudents(data);
      setTotal(count || 0);
      setLoading(false);
    }, 300);
    return () => clearTimeout(t);
  }, [search]);

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-black">Students</h1>
          <p className="text-sm text-muted-foreground">{total} registered students</p>
        </div>
      </div>

      <div className="relative max-w-sm">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
        <input
          value={search}
          onChange={e => setSearch(e.target.value)}
          placeholder="Search students..."
          className="w-full pl-9 pr-4 py-2 text-sm bg-card border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500"
        />
      </div>

      <div className="bg-card border border-border rounded-2xl overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-border bg-muted/30">
              <th className="text-left px-5 py-3 font-medium text-muted-foreground">Student</th>
              <th className="text-left px-5 py-3 font-medium text-muted-foreground hidden md:table-cell">Phone</th>
              <th className="text-left px-5 py-3 font-medium text-muted-foreground">Role</th>
              <th className="text-left px-5 py-3 font-medium text-muted-foreground hidden lg:table-cell">Joined</th>
              <th className="text-left px-5 py-3 font-medium text-muted-foreground">Status</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              Array.from({ length: 8 }).map((_, i) => (
                <tr key={i} className="border-b border-border">
                  {Array.from({ length: 5 }).map((_, j) => <td key={j} className="px-5 py-3"><div className="h-4 skeleton rounded w-20" /></td>)}
                </tr>
              ))
            ) : students.length === 0 ? (
              <tr><td colSpan={5} className="px-5 py-12 text-center text-muted-foreground">No students found</td></tr>
            ) : students.map(s => (
              <tr key={s.id} className="border-b border-border hover:bg-muted/20 transition-colors">
                <td className="px-5 py-3">
                  <div className="flex items-center gap-3">
                    <div className="w-8 h-8 rounded-full bg-brand-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                      {(s.full_name || s.email || 'U')[0].toUpperCase()}
                    </div>
                    <div>
                      <p className="font-medium">{s.full_name || 'No name'}</p>
                      <p className="text-xs text-muted-foreground">{s.email}</p>
                    </div>
                  </div>
                </td>
                <td className="px-5 py-3 hidden md:table-cell text-muted-foreground text-xs">{s.phone || '—'}</td>
                <td className="px-5 py-3">
                  <span className={cn('text-xs font-medium px-2 py-0.5 rounded-full capitalize', roleColors[s.role] || 'bg-gray-100 text-gray-600')}>
                    {s.role}
                  </span>
                </td>
                <td className="px-5 py-3 hidden lg:table-cell text-xs text-muted-foreground">
                  {format(new Date(s.created_at), 'dd MMM yyyy')}
                </td>
                <td className="px-5 py-3">
                  <span className={cn('text-xs font-medium px-2 py-0.5 rounded-full', s.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600')}>
                    {s.is_active ? 'Active' : 'Inactive'}
                  </span>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
