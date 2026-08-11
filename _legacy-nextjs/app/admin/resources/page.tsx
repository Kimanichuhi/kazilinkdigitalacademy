'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { toast } from 'sonner';
import { Plus, Edit, Trash2, Download } from 'lucide-react';
import type { Resource } from '@/lib/database.types';
import { Button } from '@/components/ui/button';

export default function AdminResourcesPage() {
  const [resources, setResources] = useState<Resource[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState<Resource | null>(null);
  const [saving, setSaving] = useState(false);
  const [formData, setFormData] = useState({ title: '', description: '', type: 'pdf', file_url: '', is_free: true, is_published: true, tags: '' });

  useEffect(() => { load(); }, []);
  async function load() {
    const { data } = await supabase.from('resources').select('*').order('order_index');
    if (data) setResources(data);
    setLoading(false);
  }
  async function handleSave() {
    if (!formData.title) { toast.error('Title required'); return; }
    setSaving(true);
    const payload = { ...formData, tags: formData.tags ? formData.tags.split(',').map(t => t.trim()).filter(Boolean) : null };
    const op = editing ? supabase.from('resources').update({ ...payload, updated_at: new Date().toISOString() }).eq('id', editing.id) : supabase.from('resources').insert(payload as any);
    const { error } = await op;
    if (error) { toast.error(error.message); } else { toast.success(editing ? 'Updated' : 'Created'); load(); setShowForm(false); }
    setSaving(false);
  }

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <div><h1 className="text-2xl font-black">Resources</h1><p className="text-sm text-muted-foreground">{resources.length} resources</p></div>
        <Button onClick={() => { setEditing(null); setFormData({ title: '', description: '', type: 'pdf', file_url: '', is_free: true, is_published: true, tags: '' }); setShowForm(true); }} className="bg-brand-500 hover:bg-brand-600 text-white gap-2">
          <Plus className="w-4 h-4" /> Add Resource
        </Button>
      </div>

      <div className="bg-card border border-border rounded-2xl overflow-hidden">
        <table className="w-full text-sm">
          <thead><tr className="border-b border-border bg-muted/30">
            <th className="text-left px-5 py-3 font-medium text-muted-foreground">Resource</th>
            <th className="text-left px-5 py-3 font-medium text-muted-foreground">Type</th>
            <th className="text-left px-5 py-3 font-medium text-muted-foreground">Downloads</th>
            <th className="text-left px-5 py-3 font-medium text-muted-foreground">Status</th>
            <th className="text-left px-5 py-3 font-medium text-muted-foreground">Actions</th>
          </tr></thead>
          <tbody>
            {loading ? Array.from({ length: 4 }).map((_, i) => <tr key={i} className="border-b"><td colSpan={5} className="px-5 py-3"><div className="h-4 skeleton rounded" /></td></tr>) :
              resources.map(r => (
                <tr key={r.id} className="border-b border-border hover:bg-muted/20 transition-colors">
                  <td className="px-5 py-3"><p className="font-medium">{r.title}</p>{r.description && <p className="text-xs text-muted-foreground line-clamp-1">{r.description}</p>}</td>
                  <td className="px-5 py-3 text-xs capitalize text-muted-foreground">{r.type}</td>
                  <td className="px-5 py-3 text-sm">{r.download_count.toLocaleString()}</td>
                  <td className="px-5 py-3"><span className={`text-xs font-medium px-2 py-0.5 rounded-full ${r.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'}`}>{r.is_published ? 'Published' : 'Draft'}</span></td>
                  <td className="px-5 py-3">
                    <div className="flex gap-1.5">
                      <button onClick={() => { setEditing(r); setFormData({ title: r.title, description: r.description || '', type: r.type, file_url: r.file_url || '', is_free: r.is_free, is_published: r.is_published, tags: r.tags?.join(', ') || '' }); setShowForm(true); }} className="p-1.5 hover:bg-muted rounded-lg"><Edit className="w-3.5 h-3.5 text-muted-foreground" /></button>
                      <button onClick={async () => { if (!confirm('Delete?')) return; await supabase.from('resources').delete().eq('id', r.id); toast.success('Deleted'); load(); }} className="p-1.5 hover:bg-red-50 rounded-lg"><Trash2 className="w-3.5 h-3.5 text-muted-foreground" /></button>
                    </div>
                  </td>
                </tr>
              ))}
          </tbody>
        </table>
      </div>

      {showForm && (
        <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
          <div className="bg-background border border-border rounded-2xl w-full max-w-lg shadow-2xl">
            <div className="flex items-center justify-between p-5 border-b border-border">
              <h2 className="font-bold">{editing ? 'Edit' : 'New'} Resource</h2>
              <button onClick={() => setShowForm(false)} className="p-2 hover:bg-muted rounded-lg">×</button>
            </div>
            <div className="p-5 space-y-3">
              <div><label className="text-xs font-medium mb-1 block">Title *</label><input value={formData.title} onChange={e => setFormData(d => ({ ...d, title: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" /></div>
              <div><label className="text-xs font-medium mb-1 block">Description</label><textarea value={formData.description} onChange={e => setFormData(d => ({ ...d, description: e.target.value }))} rows={2} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none" /></div>
              <div className="grid grid-cols-2 gap-3">
                <div><label className="text-xs font-medium mb-1 block">Type</label><select value={formData.type} onChange={e => setFormData(d => ({ ...d, type: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">{['pdf','video','template','guide','spreadsheet','ebook','audio','other'].map(t => <option key={t} value={t}>{t}</option>)}</select></div>
              </div>
              <div><label className="text-xs font-medium mb-1 block">File URL</label><input value={formData.file_url} onChange={e => setFormData(d => ({ ...d, file_url: e.target.value }))} placeholder="https://..." className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" /></div>
              <div><label className="text-xs font-medium mb-1 block">Tags (comma-separated)</label><input value={formData.tags} onChange={e => setFormData(d => ({ ...d, tags: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" /></div>
              <div className="flex gap-4">
                <label className="flex items-center gap-2 cursor-pointer text-sm"><input type="checkbox" checked={formData.is_free} onChange={e => setFormData(d => ({ ...d, is_free: e.target.checked }))} className="w-4 h-4 rounded" />Free</label>
                <label className="flex items-center gap-2 cursor-pointer text-sm"><input type="checkbox" checked={formData.is_published} onChange={e => setFormData(d => ({ ...d, is_published: e.target.checked }))} className="w-4 h-4 rounded" />Published</label>
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
