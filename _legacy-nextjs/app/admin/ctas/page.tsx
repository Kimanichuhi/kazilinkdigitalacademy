'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { toast } from 'sonner';
import { Plus, Edit, Trash2 } from 'lucide-react';
import type { CTA } from '@/lib/database.types';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

const placements = ['homepage','about','programs','pricing','booking','blog','contact'];

export default function AdminCtasPage() {
  const [ctas, setCtas] = useState<CTA[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState<CTA | null>(null);
  const [saving, setSaving] = useState(false);
  const [formData, setFormData] = useState({
    name: '', title: '', subtitle: '', description: '', background_color: '#0f172a',
    button_one_text: '', button_one_link: '', button_one_style: 'primary',
    button_two_text: '', button_two_link: '', button_two_style: 'outline',
    placement: ['homepage'] as string[], priority: '0', is_active: true,
  });

  useEffect(() => { load(); }, []);

  async function load() {
    const { data } = await supabase.from('ctas').select('*').order('priority', { ascending: false });
    if (data) setCtas(data);
    setLoading(false);
  }

  function openEdit(c: CTA) {
    setEditing(c);
    setFormData({
      name: c.name, title: c.title, subtitle: c.subtitle || '', description: c.description || '',
      background_color: c.background_color || '#0f172a', button_one_text: c.button_one_text || '',
      button_one_link: c.button_one_link || '', button_one_style: c.button_one_style || 'primary',
      button_two_text: c.button_two_text || '', button_two_link: c.button_two_link || '',
      button_two_style: c.button_two_style || 'outline', placement: c.placement || [],
      priority: c.priority.toString(), is_active: c.is_active,
    });
    setShowForm(true);
  }

  async function handleSave() {
    if (!formData.name || !formData.title) { toast.error('Name and title required'); return; }
    setSaving(true);
    const payload = { ...formData, priority: parseInt(formData.priority) || 0 };
    const op = editing ? supabase.from('ctas').update({ ...payload, updated_at: new Date().toISOString() }).eq('id', editing.id) : supabase.from('ctas').insert(payload as any);
    const { error } = await op;
    if (error) { toast.error(error.message); } else { toast.success(editing ? 'Updated' : 'Created'); load(); setShowForm(false); }
    setSaving(false);
  }

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <div><h1 className="text-2xl font-black">CTA Manager</h1><p className="text-sm text-muted-foreground">{ctas.length} CTAs</p></div>
        <Button onClick={() => { setEditing(null); setFormData({ name: '', title: '', subtitle: '', description: '', background_color: '#0f172a', button_one_text: '', button_one_link: '', button_one_style: 'primary', button_two_text: '', button_two_link: '', button_two_style: 'outline', placement: ['homepage'], priority: '0', is_active: true }); setShowForm(true); }} className="bg-brand-500 hover:bg-brand-600 text-white gap-2">
          <Plus className="w-4 h-4" /> New CTA
        </Button>
      </div>

      <div className="space-y-3">
        {loading ? Array.from({ length: 3 }).map((_, i) => <div key={i} className="h-20 skeleton rounded-2xl" />) :
          ctas.map(c => (
            <div key={c.id} className="bg-card border border-border rounded-2xl p-5 flex items-center justify-between gap-4">
              <div className="flex-1 min-w-0">
                <p className="font-bold">{c.name}</p>
                <p className="text-sm text-muted-foreground line-clamp-1">{c.title}</p>
                <div className="flex gap-2 mt-1.5 text-xs text-muted-foreground">
                  <span>Placements: {c.placement?.join(', ')}</span>
                  <span>|</span>
                  <span>Priority: {c.priority}</span>
                </div>
              </div>
              <div className="flex items-center gap-2 flex-shrink-0">
                <span className={cn('text-xs font-medium px-2 py-0.5 rounded-full', c.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500')}>
                  {c.is_active ? 'Active' : 'Inactive'}
                </span>
                <button onClick={() => openEdit(c)} className="p-1.5 hover:bg-muted rounded-lg"><Edit className="w-3.5 h-3.5 text-muted-foreground" /></button>
                <button onClick={async () => { if (!confirm('Delete?')) return; await supabase.from('ctas').delete().eq('id', c.id); toast.success('Deleted'); load(); }} className="p-1.5 hover:bg-red-50 rounded-lg"><Trash2 className="w-3.5 h-3.5 text-muted-foreground" /></button>
              </div>
            </div>
          ))}
      </div>

      {showForm && (
        <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
          <div className="bg-background border border-border rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
            <div className="flex items-center justify-between p-5 border-b border-border">
              <h2 className="font-bold">{editing ? 'Edit' : 'New'} CTA</h2>
              <button onClick={() => setShowForm(false)} className="p-2 hover:bg-muted rounded-lg">×</button>
            </div>
            <div className="p-5 space-y-4">
              <div className="grid sm:grid-cols-2 gap-4">
                {[
                  { k: 'name', l: 'Name *' }, { k: 'title', l: 'Title *' }, { k: 'subtitle', l: 'Subtitle' },
                  { k: 'button_one_text', l: 'Button 1 Text' }, { k: 'button_one_link', l: 'Button 1 Link' },
                  { k: 'button_two_text', l: 'Button 2 Text' }, { k: 'button_two_link', l: 'Button 2 Link' },
                  { k: 'priority', l: 'Priority' },
                ].map(f => (
                  <div key={f.k}>
                    <label className="text-xs font-medium mb-1 block">{f.l}</label>
                    <input value={(formData as any)[f.k]} onChange={e => setFormData(d => ({ ...d, [f.k]: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                  </div>
                ))}
                <div className="sm:col-span-2">
                  <label className="text-xs font-medium mb-1 block">Description</label>
                  <textarea value={formData.description} onChange={e => setFormData(d => ({ ...d, description: e.target.value }))} rows={2} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Background Color</label>
                  <div className="flex gap-2">
                    <input type="color" value={formData.background_color} onChange={e => setFormData(d => ({ ...d, background_color: e.target.value }))} className="w-10 h-9 border border-border rounded-lg cursor-pointer" />
                    <input value={formData.background_color} onChange={e => setFormData(d => ({ ...d, background_color: e.target.value }))} className="flex-1 px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                  </div>
                </div>
                <div className="sm:col-span-2">
                  <label className="text-xs font-medium mb-1.5 block">Placements</label>
                  <div className="flex flex-wrap gap-2">
                    {placements.map(p => (
                      <label key={p} className="flex items-center gap-1.5 cursor-pointer text-sm">
                        <input type="checkbox" checked={formData.placement.includes(p)} onChange={e => setFormData(d => ({ ...d, placement: e.target.checked ? [...d.placement, p] : d.placement.filter(x => x !== p) }))} className="w-3.5 h-3.5 rounded" />
                        <span className="capitalize">{p}</span>
                      </label>
                    ))}
                  </div>
                </div>
                <div>
                  <label className="flex items-center gap-2 cursor-pointer text-sm mt-1"><input type="checkbox" checked={formData.is_active} onChange={e => setFormData(d => ({ ...d, is_active: e.target.checked }))} className="w-4 h-4 rounded" />Active</label>
                </div>
              </div>
              <div className="flex justify-end gap-3 pt-2 border-t border-border">
                <Button variant="outline" onClick={() => setShowForm(false)}>Cancel</Button>
                <Button onClick={handleSave} disabled={saving} className="bg-brand-500 hover:bg-brand-600 text-white">{saving ? 'Saving...' : (editing ? 'Update' : 'Create')}</Button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
