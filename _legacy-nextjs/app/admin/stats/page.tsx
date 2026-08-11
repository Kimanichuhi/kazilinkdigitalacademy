'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { toast } from 'sonner';
import {
  Plus, Edit, Trash2, Users, BookOpen, TrendingUp, Globe, Award, DollarSign, Zap, Star,
} from 'lucide-react';
import type { Statistic } from '@/lib/database.types';
import { Button } from '@/components/ui/button';

const iconOptions = ['Users', 'BookOpen', 'TrendingUp', 'Globe', 'Award', 'DollarSign', 'Zap', 'Star'] as const;

const iconMap: Record<string, React.ReactNode> = {
  Users: <Users className="w-5 h-5" />,
  BookOpen: <BookOpen className="w-5 h-5" />,
  TrendingUp: <TrendingUp className="w-5 h-5" />,
  Globe: <Globe className="w-5 h-5" />,
  Award: <Award className="w-5 h-5" />,
  DollarSign: <DollarSign className="w-5 h-5" />,
  Zap: <Zap className="w-5 h-5" />,
  Star: <Star className="w-5 h-5" />,
};

const emptyForm = { key: '', label: '', value: '', icon: 'TrendingUp', description: '', order_index: '0', is_active: true };

export default function AdminStatsPage() {
  const [stats, setStats] = useState<Statistic[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState<Statistic | null>(null);
  const [saving, setSaving] = useState(false);
  const [formData, setFormData] = useState(emptyForm);

  useEffect(() => { load(); }, []);

  async function load() {
    const { data } = await supabase.from('statistics').select('*').order('order_index');
    if (data) setStats(data);
    setLoading(false);
  }

  function openCreate() {
    setEditing(null);
    setFormData({ ...emptyForm, order_index: (stats.length + 1).toString() });
    setShowForm(true);
  }

  function openEdit(stat: Statistic) {
    setEditing(stat);
    setFormData({
      key: stat.key || '',
      label: stat.label,
      value: stat.value,
      icon: stat.icon || 'TrendingUp',
      description: stat.description || '',
      order_index: stat.order_index.toString(),
      is_active: stat.is_active,
    });
    setShowForm(true);
  }

  async function handleSave() {
    if (!formData.label || !formData.value) { toast.error('Label and value are required'); return; }
    setSaving(true);
    const payload = {
      key: formData.key.trim() || null,
      label: formData.label,
      value: formData.value,
      icon: formData.icon,
      description: formData.description || null,
      order_index: parseInt(formData.order_index) || 0,
      is_active: formData.is_active,
    };
    const op = editing
      ? supabase.from('statistics').update(payload).eq('id', editing.id)
      : supabase.from('statistics').insert(payload);
    const { error } = await op;
    if (error) { toast.error(error.message); } else { toast.success(editing ? 'Stat updated' : 'Stat created'); load(); setShowForm(false); }
    setSaving(false);
  }

  async function deleteStat(id: string) {
    if (!confirm('Delete this stat? Any page referencing its key will fall back to a default value.')) return;
    await supabase.from('statistics').delete().eq('id', id);
    toast.success('Deleted'); load();
  }

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-black">Stats</h1>
          <p className="text-sm text-muted-foreground">{stats.length} stats — shown in the homepage stats bar, and by key on the About, Success Stories, and hero sections</p>
        </div>
        <Button onClick={openCreate} className="bg-brand-500 hover:bg-brand-600 text-white gap-2"><Plus className="w-4 h-4" /> Add Stat</Button>
      </div>

      <div className="space-y-2">
        {loading ? Array.from({ length: 4 }).map((_, i) => <div key={i} className="h-16 skeleton rounded-xl" />) :
          stats.map(stat => (
            <div key={stat.id} className="bg-card border border-border rounded-xl px-5 py-4 flex items-center justify-between gap-4">
              <div className="flex items-center gap-4 flex-1 min-w-0">
                <div className="w-10 h-10 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                  {stat.icon && iconMap[stat.icon] ? iconMap[stat.icon] : <TrendingUp className="w-5 h-5" />}
                </div>
                <div className="min-w-0">
                  <div className="flex items-center gap-2 flex-wrap">
                    <span className="font-bold text-sm">{stat.value}</span>
                    <span className="text-sm text-muted-foreground">{stat.label}</span>
                    {stat.key && <span className="text-[10px] font-mono text-brand-600 bg-brand-50 px-1.5 py-0.5 rounded">{stat.key}</span>}
                    {!stat.is_active && <span className="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">Inactive</span>}
                  </div>
                  {stat.description && <p className="text-xs text-muted-foreground mt-1 line-clamp-1">{stat.description}</p>}
                </div>
              </div>
              <div className="flex gap-1.5 flex-shrink-0">
                <button onClick={() => openEdit(stat)} className="p-1.5 hover:bg-muted rounded-lg transition-colors"><Edit className="w-3.5 h-3.5 text-muted-foreground" /></button>
                <button onClick={() => deleteStat(stat.id)} className="p-1.5 hover:bg-red-50 rounded-lg transition-colors"><Trash2 className="w-3.5 h-3.5 text-muted-foreground hover:text-red-600" /></button>
              </div>
            </div>
          ))}
        {!loading && stats.length === 0 && (
          <div className="text-center py-12 text-muted-foreground text-sm">No stats yet. Add one to show it in the homepage stats bar.</div>
        )}
      </div>

      {showForm && (
        <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
          <div className="bg-background border border-border rounded-2xl w-full max-w-lg shadow-2xl">
            <div className="flex items-center justify-between p-5 border-b border-border">
              <h2 className="font-bold">{editing ? 'Edit Stat' : 'New Stat'}</h2>
              <button onClick={() => setShowForm(false)} className="p-2 hover:bg-muted rounded-lg">×</button>
            </div>
            <div className="p-5 space-y-4">
              <div className="grid sm:grid-cols-2 gap-4">
                <div>
                  <label className="text-xs font-medium mb-1 block">Label *</label>
                  <input value={formData.label} onChange={e => setFormData(d => ({ ...d, label: e.target.value }))} placeholder="Students Trained" className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Value *</label>
                  <input value={formData.value} onChange={e => setFormData(d => ({ ...d, value: e.target.value }))} placeholder="12,500+" className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Icon</label>
                  <select value={formData.icon} onChange={e => setFormData(d => ({ ...d, icon: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                    {iconOptions.map(opt => <option key={opt} value={opt}>{opt}</option>)}
                  </select>
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Order</label>
                  <input type="number" value={formData.order_index} onChange={e => setFormData(d => ({ ...d, order_index: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div className="sm:col-span-2">
                  <label className="text-xs font-medium mb-1 block">Key (optional)</label>
                  <input value={formData.key} onChange={e => setFormData(d => ({ ...d, key: e.target.value }))} placeholder="students_trained" className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 font-mono" />
                  <p className="text-xs text-muted-foreground mt-1">Set a key to bind this stat to a specific spot on the site (e.g. <code className="font-mono">students_trained</code>, <code className="font-mono">success_rate</code>, <code className="font-mono">avg_rating</code> are used on the homepage hero, About page, and Success Stories). Leave blank for a stat that only appears in the homepage stats bar.</p>
                </div>
                <div className="sm:col-span-2">
                  <label className="text-xs font-medium mb-1 block">Description</label>
                  <input value={formData.description} onChange={e => setFormData(d => ({ ...d, description: e.target.value }))} placeholder="Graduates across all programs" className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div>
                  <label className="flex items-center gap-2 cursor-pointer text-sm">
                    <input type="checkbox" checked={formData.is_active} onChange={e => setFormData(d => ({ ...d, is_active: e.target.checked }))} className="w-4 h-4 rounded" />
                    Active
                  </label>
                </div>
              </div>
              <div className="flex justify-end gap-3 pt-2 border-t border-border">
                <Button variant="outline" onClick={() => setShowForm(false)}>Cancel</Button>
                <Button onClick={handleSave} disabled={saving} className="bg-brand-500 hover:bg-brand-600 text-white">
                  {saving ? 'Saving...' : (editing ? 'Update' : 'Create')}
                </Button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
