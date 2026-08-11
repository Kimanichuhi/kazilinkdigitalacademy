'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { toast } from 'sonner';
import { Plus, Edit, Trash2 } from 'lucide-react';
import type { Trainer } from '@/lib/database.types';
import { Button } from '@/components/ui/button';
import { Star } from 'lucide-react';
import { ImageUpload } from '@/components/admin/ImageUpload';

export default function AdminTrainersPage() {
  const [trainers, setTrainers] = useState<Trainer[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState<Trainer | null>(null);
  const [saving, setSaving] = useState(false);
  const [formData, setFormData] = useState({
    full_name: '', title: '', bio: '', avatar_url: '', email: '',
    phone: '', specializations: '', is_featured: false, is_active: true,
  });

  useEffect(() => { load(); }, []);

  async function load() {
    const { data } = await supabase.from('trainers').select('*').order('order_index');
    if (data) setTrainers(data);
    setLoading(false);
  }

  function openEdit(t: Trainer) {
    setEditing(t);
    setFormData({ full_name: t.full_name, title: t.title || '', bio: t.bio || '', avatar_url: t.avatar_url || '', email: t.email || '', phone: t.phone || '', specializations: t.specializations?.join(', ') || '', is_featured: t.is_featured, is_active: t.is_active });
    setShowForm(true);
  }

  async function deleteTrainer(id: string) {
    if (!confirm('Delete this trainer? This cannot be undone.')) return;
    // .select() lets us confirm a row was actually removed — a plain
    // .delete() reports success even when RLS silently blocks it (0 rows affected, no error).
    const { data, error } = await supabase.from('trainers').delete().eq('id', id).select();
    if (error) { toast.error(error.message); return; }
    if (!data || data.length === 0) { toast.error('Delete failed — you may not have permission to delete this trainer.'); return; }
    toast.success('Deleted');
    load();
  }

  async function handleSave() {
    if (!formData.full_name) { toast.error('Name required'); return; }
    setSaving(true);
    const payload = { ...formData, specializations: formData.specializations ? formData.specializations.split(',').map(s => s.trim()).filter(Boolean) : null };
    const op = editing ? supabase.from('trainers').update({ ...payload, updated_at: new Date().toISOString() }).eq('id', editing.id) : supabase.from('trainers').insert(payload as any);
    const { error } = await op;
    if (error) { toast.error(error.message); } else { toast.success(editing ? 'Updated' : 'Created'); load(); setShowForm(false); }
    setSaving(false);
  }

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <div><h1 className="text-2xl font-black">Trainers</h1><p className="text-sm text-muted-foreground">{trainers.length} trainers</p></div>
        <Button onClick={() => { setEditing(null); setFormData({ full_name: '', title: '', bio: '', avatar_url: '', email: '', phone: '', specializations: '', is_featured: false, is_active: true }); setShowForm(true); }} className="bg-brand-500 hover:bg-brand-600 text-white gap-2">
          <Plus className="w-4 h-4" /> Add Trainer
        </Button>
      </div>

      <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        {loading ? Array.from({ length: 4 }).map((_, i) => <div key={i} className="h-40 skeleton rounded-2xl" />) :
          trainers.map(t => (
            <div key={t.id} className="bg-card border border-border rounded-2xl p-5">
              <div className="flex items-start gap-3 mb-3">
                {t.avatar_url ? <img src={t.avatar_url} alt="" className="w-12 h-12 rounded-full object-cover flex-shrink-0" /> : <div className="w-12 h-12 rounded-full bg-brand-500 text-white font-bold flex items-center justify-center flex-shrink-0">{t.full_name[0]}</div>}
                <div className="flex-1 min-w-0">
                  <p className="font-bold truncate">{t.full_name}</p>
                  {t.title && <p className="text-xs text-brand-600">{t.title}</p>}
                </div>
                <div className="flex gap-1 flex-shrink-0">
                  <button onClick={() => openEdit(t)} className="p-1.5 hover:bg-muted rounded-lg"><Edit className="w-3.5 h-3.5 text-muted-foreground" /></button>
                  <button onClick={() => deleteTrainer(t.id)} className="p-1.5 hover:bg-red-50 rounded-lg"><Trash2 className="w-3.5 h-3.5 text-muted-foreground" /></button>
                </div>
              </div>
              {t.specializations && <div className="flex flex-wrap gap-1">{t.specializations.slice(0, 3).map(s => <span key={s} className="text-[10px] bg-brand-50 text-brand-700 px-2 py-0.5 rounded-full">{s}</span>)}</div>}
              <div className="flex items-center justify-between mt-3 text-xs text-muted-foreground">
                {t.rating > 0 && <span className="flex items-center gap-1 text-amber-500"><Star className="w-3 h-3 fill-current" />{t.rating.toFixed(1)}</span>}
                <span className={`px-2 py-0.5 rounded-full ${t.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`}>{t.is_active ? 'Active' : 'Inactive'}</span>
              </div>
            </div>
          ))}
      </div>

      {showForm && (
        <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
          <div className="bg-background border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
            <div className="flex items-center justify-between p-5 border-b border-border">
              <h2 className="font-bold">{editing ? 'Edit' : 'New'} Trainer</h2>
              <button onClick={() => setShowForm(false)} className="p-2 hover:bg-muted rounded-lg">×</button>
            </div>
            <div className="p-5 space-y-3">
              <ImageUpload value={formData.avatar_url} onChange={url => setFormData(d => ({ ...d, avatar_url: url }))} folder="trainers" label="Avatar Photo" shape="circle" />
              {[{ k: 'full_name', l: 'Full Name *' }, { k: 'title', l: 'Title/Role' }, { k: 'email', l: 'Email' }, { k: 'phone', l: 'Phone' }, { k: 'specializations', l: 'Specializations (comma-separated)' }].map(f => (
                <div key={f.k}>
                  <label className="text-xs font-medium mb-1 block">{f.l}</label>
                  <input value={(formData as any)[f.k]} onChange={e => setFormData(d => ({ ...d, [f.k]: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
              ))}
              <div>
                <label className="text-xs font-medium mb-1 block">Bio</label>
                <textarea value={formData.bio} onChange={e => setFormData(d => ({ ...d, bio: e.target.value }))} rows={3} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none" />
              </div>
              <div className="flex gap-4">
                <label className="flex items-center gap-2 cursor-pointer text-sm"><input type="checkbox" checked={formData.is_featured} onChange={e => setFormData(d => ({ ...d, is_featured: e.target.checked }))} className="w-4 h-4 rounded" />Featured</label>
                <label className="flex items-center gap-2 cursor-pointer text-sm"><input type="checkbox" checked={formData.is_active} onChange={e => setFormData(d => ({ ...d, is_active: e.target.checked }))} className="w-4 h-4 rounded" />Active</label>
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
