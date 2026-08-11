'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { toast } from 'sonner';
import { Plus, Edit, Trash2, Search } from 'lucide-react';
import type { Testimonial } from '@/lib/database.types';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { Star } from 'lucide-react';
import { ImageUpload } from '@/components/admin/ImageUpload';

export default function AdminTestimonialsPage() {
  const [items, setItems] = useState<Testimonial[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState<Testimonial | null>(null);
  const [saving, setSaving] = useState(false);
  const [formData, setFormData] = useState({
    student_name: '', student_title: '', student_avatar_url: '', content: '',
    rating: '5', income_before: '', income_after: '', is_featured: false, is_published: true, order_index: '0',
  });

  useEffect(() => { load(); }, []);
  async function load() {
    const { data } = await supabase.from('testimonials').select('*').order('order_index');
    if (data) setItems(data);
    setLoading(false);
  }
  function openEdit(t: Testimonial) {
    setEditing(t);
    setFormData({ student_name: t.student_name, student_title: t.student_title || '', student_avatar_url: t.student_avatar_url || '', content: t.content, rating: (t.rating || 5).toString(), income_before: t.income_before || '', income_after: t.income_after || '', is_featured: t.is_featured, is_published: t.is_published, order_index: t.order_index.toString() });
    setShowForm(true);
  }
  async function handleSave() {
    if (!formData.student_name || !formData.content) { toast.error('Name and content required'); return; }
    setSaving(true);
    const payload = { ...formData, rating: parseInt(formData.rating), order_index: parseInt(formData.order_index) || 0 };
    const op = editing ? supabase.from('testimonials').update(payload).eq('id', editing.id) : supabase.from('testimonials').insert(payload);
    const { error } = await op;
    if (error) { toast.error(error.message); } else { toast.success(editing ? 'Updated' : 'Created'); load(); setShowForm(false); }
    setSaving(false);
  }
  async function del(id: string) {
    if (!confirm('Delete?')) return;
    await supabase.from('testimonials').delete().eq('id', id);
    toast.success('Deleted'); load();
  }

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <div><h1 className="text-2xl font-black">Testimonials</h1><p className="text-sm text-muted-foreground">{items.length} testimonials</p></div>
        <Button onClick={() => { setEditing(null); setFormData({ student_name: '', student_title: '', student_avatar_url: '', content: '', rating: '5', income_before: '', income_after: '', is_featured: false, is_published: true, order_index: (items.length + 1).toString() }); setShowForm(true); }} className="bg-brand-500 hover:bg-brand-600 text-white gap-2">
          <Plus className="w-4 h-4" /> Add Testimonial
        </Button>
      </div>
      <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {loading ? Array.from({ length: 4 }).map((_, i) => <div key={i} className="h-40 skeleton rounded-2xl" />) :
          items.map(t => (
            <div key={t.id} className="bg-card border border-border rounded-2xl p-4">
              <div className="flex items-start justify-between mb-3">
                <div className="flex items-center gap-2">
                  {t.student_avatar_url && <img src={t.student_avatar_url} alt="" className="w-8 h-8 rounded-full object-cover" />}
                  <div>
                    <p className="font-medium text-sm">{t.student_name}</p>
                    {t.student_title && <p className="text-xs text-muted-foreground">{t.student_title}</p>}
                  </div>
                </div>
                <div className="flex gap-1">
                  <button onClick={() => openEdit(t)} className="p-1 hover:bg-muted rounded transition-colors"><Edit className="w-3.5 h-3.5 text-muted-foreground" /></button>
                  <button onClick={() => del(t.id)} className="p-1 hover:bg-red-50 rounded transition-colors"><Trash2 className="w-3.5 h-3.5 text-muted-foreground" /></button>
                </div>
              </div>
              <p className="text-xs text-muted-foreground line-clamp-3">{t.content}</p>
              <div className="flex items-center gap-2 mt-3">
                {t.rating && <div className="flex gap-0.5">{Array.from({ length: t.rating }).map((_, i) => <Star key={i} className="w-3 h-3 fill-amber-400 text-amber-400" />)}</div>}
                <span className={cn('ml-auto text-xs px-2 py-0.5 rounded-full', t.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500')}>
                  {t.is_published ? 'Published' : 'Draft'}
                </span>
              </div>
            </div>
          ))}
      </div>

      {showForm && (
        <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
          <div className="bg-background border border-border rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
            <div className="flex items-center justify-between p-5 border-b border-border">
              <h2 className="font-bold">{editing ? 'Edit' : 'New'} Testimonial</h2>
              <button onClick={() => setShowForm(false)} className="p-2 hover:bg-muted rounded-lg">×</button>
            </div>
            <div className="p-5 space-y-3">
              <ImageUpload value={formData.student_avatar_url} onChange={url => setFormData(d => ({ ...d, student_avatar_url: url }))} folder="testimonials" label="Avatar Photo" shape="circle" />
              {[{ key: 'student_name', label: 'Student Name *' }, { key: 'student_title', label: 'Title/Role' }, { key: 'income_before', label: 'Income Before' }, { key: 'income_after', label: 'Income After' }].map(f => (
                <div key={f.key}>
                  <label className="text-xs font-medium mb-1 block">{f.label}</label>
                  <input value={(formData as any)[f.key]} onChange={e => setFormData(d => ({ ...d, [f.key]: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
              ))}
              <div>
                <label className="text-xs font-medium mb-1 block">Content *</label>
                <textarea value={formData.content} onChange={e => setFormData(d => ({ ...d, content: e.target.value }))} rows={4} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none" />
              </div>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="text-xs font-medium mb-1 block">Rating</label>
                  <select value={formData.rating} onChange={e => setFormData(d => ({ ...d, rating: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                    {[5,4,3,2,1].map(r => <option key={r} value={r}>{r} Stars</option>)}
                  </select>
                </div>
                <div className="flex items-end gap-4 pb-1">
                  <label className="flex items-center gap-2 cursor-pointer text-sm"><input type="checkbox" checked={formData.is_published} onChange={e => setFormData(d => ({ ...d, is_published: e.target.checked }))} className="w-4 h-4 rounded" />Published</label>
                  <label className="flex items-center gap-2 cursor-pointer text-sm"><input type="checkbox" checked={formData.is_featured} onChange={e => setFormData(d => ({ ...d, is_featured: e.target.checked }))} className="w-4 h-4 rounded" />Featured</label>
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
