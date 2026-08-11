'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { toast } from 'sonner';
import { Plus, Edit, Trash2, ChevronDown, ChevronUp } from 'lucide-react';
import type { FAQ } from '@/lib/database.types';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

export default function AdminFaqsPage() {
  const [faqs, setFaqs] = useState<FAQ[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState<FAQ | null>(null);
  const [saving, setSaving] = useState(false);
  const [formData, setFormData] = useState({ category: 'general', question: '', answer: '', order_index: '0', is_published: true });

  useEffect(() => { load(); }, []);

  async function load() {
    const { data } = await supabase.from('faqs').select('*').order('order_index');
    if (data) setFaqs(data);
    setLoading(false);
  }

  function openCreate() {
    setEditing(null);
    setFormData({ category: 'general', question: '', answer: '', order_index: (faqs.length + 1).toString(), is_published: true });
    setShowForm(true);
  }

  function openEdit(faq: FAQ) {
    setEditing(faq);
    setFormData({ category: faq.category, question: faq.question, answer: faq.answer, order_index: faq.order_index.toString(), is_published: faq.is_published });
    setShowForm(true);
  }

  async function handleSave() {
    if (!formData.question || !formData.answer) { toast.error('Question and answer required'); return; }
    setSaving(true);
    const payload = { ...formData, order_index: parseInt(formData.order_index) || 0 };
    const op = editing
      ? supabase.from('faqs').update({ ...payload, updated_at: new Date().toISOString() }).eq('id', editing.id)
      : supabase.from('faqs').insert(payload);
    const { error } = await op;
    if (error) { toast.error(error.message); } else { toast.success(editing ? 'FAQ updated' : 'FAQ created'); load(); setShowForm(false); }
    setSaving(false);
  }

  async function deleteFaq(id: string) {
    if (!confirm('Delete this FAQ?')) return;
    await supabase.from('faqs').delete().eq('id', id);
    toast.success('Deleted'); load();
  }

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <div><h1 className="text-2xl font-black">FAQs</h1><p className="text-sm text-muted-foreground">{faqs.length} questions</p></div>
        <Button onClick={openCreate} className="bg-brand-500 hover:bg-brand-600 text-white gap-2"><Plus className="w-4 h-4" /> Add FAQ</Button>
      </div>

      <div className="space-y-2">
        {loading ? Array.from({ length: 5 }).map((_, i) => <div key={i} className="h-16 skeleton rounded-xl" />) :
          faqs.map(faq => (
            <div key={faq.id} className="bg-card border border-border rounded-xl px-5 py-4 flex items-start justify-between gap-4">
              <div className="flex-1 min-w-0">
                <div className="flex items-center gap-2 mb-1">
                  <span className="text-xs text-brand-600 bg-brand-50 px-2 py-0.5 rounded-full capitalize">{faq.category}</span>
                  {!faq.is_published && <span className="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">Draft</span>}
                </div>
                <p className="font-medium text-sm">{faq.question}</p>
                <p className="text-xs text-muted-foreground mt-1 line-clamp-1">{faq.answer}</p>
              </div>
              <div className="flex gap-1.5 flex-shrink-0">
                <button onClick={() => openEdit(faq)} className="p-1.5 hover:bg-muted rounded-lg transition-colors"><Edit className="w-3.5 h-3.5 text-muted-foreground" /></button>
                <button onClick={() => deleteFaq(faq.id)} className="p-1.5 hover:bg-red-50 rounded-lg transition-colors"><Trash2 className="w-3.5 h-3.5 text-muted-foreground hover:text-red-600" /></button>
              </div>
            </div>
          ))}
      </div>

      {showForm && (
        <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
          <div className="bg-background border border-border rounded-2xl w-full max-w-lg shadow-2xl">
            <div className="flex items-center justify-between p-5 border-b border-border">
              <h2 className="font-bold">{editing ? 'Edit FAQ' : 'New FAQ'}</h2>
              <button onClick={() => setShowForm(false)} className="p-2 hover:bg-muted rounded-lg">×</button>
            </div>
            <div className="p-5 space-y-4">
              <div className="grid sm:grid-cols-2 gap-4">
                <div>
                  <label className="text-xs font-medium mb-1 block">Category</label>
                  <input value={formData.category} onChange={e => setFormData(d => ({ ...d, category: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Order</label>
                  <input type="number" value={formData.order_index} onChange={e => setFormData(d => ({ ...d, order_index: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div className="sm:col-span-2">
                  <label className="text-xs font-medium mb-1 block">Question *</label>
                  <input value={formData.question} onChange={e => setFormData(d => ({ ...d, question: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div className="sm:col-span-2">
                  <label className="text-xs font-medium mb-1 block">Answer *</label>
                  <textarea rows={5} value={formData.answer} onChange={e => setFormData(d => ({ ...d, answer: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none" />
                </div>
                <div>
                  <label className="flex items-center gap-2 cursor-pointer text-sm">
                    <input type="checkbox" checked={formData.is_published} onChange={e => setFormData(d => ({ ...d, is_published: e.target.checked }))} className="w-4 h-4 rounded" />
                    Published
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
