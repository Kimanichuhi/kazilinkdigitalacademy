'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { toast } from 'sonner';
import { Shield, Edit } from 'lucide-react';
import type { Profile } from '@/lib/database.types';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

const roles = ['student', 'trainer', 'content_manager', 'marketing', 'finance', 'support', 'admin', 'super_admin'];
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

export default function AdminUsersPage() {
  const [users, setUsers] = useState<Profile[]>([]);
  const [loading, setLoading] = useState(true);
  const [editingUser, setEditingUser] = useState<Profile | null>(null);
  const [newRole, setNewRole] = useState('');
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    supabase.from('profiles').select('*').order('created_at', { ascending: false }).limit(100).then(({ data }) => {
      if (data) setUsers(data);
      setLoading(false);
    });
  }, []);

  async function updateRole() {
    if (!editingUser || !newRole) return;
    setSaving(true);
    const { error } = await supabase.from('profiles').update({ role: newRole as Profile['role'] }).eq('id', editingUser.id);
    if (error) { toast.error(error.message); } else { toast.success('Role updated'); setUsers(u => u.map(p => p.id === editingUser.id ? { ...p, role: newRole as Profile['role'] } : p)); setEditingUser(null); }
    setSaving(false);
  }

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center gap-3">
        <div>
          <h1 className="text-2xl font-black">Users & Permissions</h1>
          <p className="text-sm text-muted-foreground">{users.length} users</p>
        </div>
      </div>

      <div className="bg-card border border-border rounded-2xl overflow-hidden">
        <table className="w-full text-sm">
          <thead><tr className="border-b border-border bg-muted/30">
            <th className="text-left px-5 py-3 font-medium text-muted-foreground">User</th>
            <th className="text-left px-5 py-3 font-medium text-muted-foreground">Role</th>
            <th className="text-left px-5 py-3 font-medium text-muted-foreground">Status</th>
            <th className="text-left px-5 py-3 font-medium text-muted-foreground">Actions</th>
          </tr></thead>
          <tbody>
            {loading ? Array.from({ length: 6 }).map((_, i) => <tr key={i} className="border-b"><td colSpan={4} className="px-5 py-3"><div className="h-4 skeleton rounded" /></td></tr>) :
              users.map(u => (
                <tr key={u.id} className="border-b border-border hover:bg-muted/20 transition-colors">
                  <td className="px-5 py-3">
                    <div className="flex items-center gap-3">
                      <div className="w-8 h-8 rounded-full bg-brand-500 flex items-center justify-center text-white text-xs font-bold">{(u.full_name || u.email || 'U')[0].toUpperCase()}</div>
                      <div>
                        <p className="font-medium">{u.full_name || '—'}</p>
                        <p className="text-xs text-muted-foreground">{u.email}</p>
                      </div>
                    </div>
                  </td>
                  <td className="px-5 py-3"><span className={cn('text-xs font-medium px-2 py-0.5 rounded-full capitalize', roleColors[u.role] || 'bg-gray-100 text-gray-600')}>{u.role.replace('_', ' ')}</span></td>
                  <td className="px-5 py-3"><span className={cn('text-xs font-medium px-2 py-0.5 rounded-full', u.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600')}>{u.is_active ? 'Active' : 'Inactive'}</span></td>
                  <td className="px-5 py-3">
                    <button onClick={() => { setEditingUser(u); setNewRole(u.role); }} className="flex items-center gap-1 text-xs text-brand-600 hover:underline">
                      <Edit className="w-3 h-3" /> Change Role
                    </button>
                  </td>
                </tr>
              ))}
          </tbody>
        </table>
      </div>

      {editingUser && (
        <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
          <div className="bg-background border border-border rounded-2xl w-full max-w-sm shadow-2xl p-6">
            <h2 className="font-bold mb-4">Change Role: {editingUser.full_name || editingUser.email}</h2>
            <select value={newRole} onChange={e => setNewRole(e.target.value)} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 mb-4">
              {roles.map(r => <option key={r} value={r}>{r.replace('_', ' ')}</option>)}
            </select>
            <div className="flex gap-3">
              <Button variant="outline" className="flex-1" onClick={() => setEditingUser(null)}>Cancel</Button>
              <Button onClick={updateRole} disabled={saving} className="flex-1 bg-brand-500 hover:bg-brand-600 text-white">{saving ? 'Saving...' : 'Update Role'}</Button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
