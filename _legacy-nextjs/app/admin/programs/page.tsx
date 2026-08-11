'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { toast } from 'sonner';
import { Plus, Edit, Trash2, Eye, ToggleLeft, ToggleRight, Search } from 'lucide-react';
import type { Program, ProgramCategory } from '@/lib/database.types';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import Link from 'next/link';
import { ImageUpload } from '@/components/admin/ImageUpload';

export default function AdminProgramsPage() {
  const [programs, setPrograms] = useState<(Program & { program_categories: ProgramCategory | null })[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [showForm, setShowForm] = useState(false);
  const [editingProgram, setEditingProgram] = useState<Program | null>(null);
  const [categories, setCategories] = useState<ProgramCategory[]>([]);
  const [saving, setSaving] = useState(false);
  const [formData, setFormData] = useState({
    title: '', slug: '', subtitle: '', short_description: '', description: '',
    thumbnail_url: '', duration_weeks: '', duration_label: '', level: 'beginner',
    delivery_mode: 'online', price: '', original_price: '', currency: 'KES',
    category_id: '', is_featured: false, is_published: false,
  });

  useEffect(() => {
    loadData();
    // Fetch every category (not just active ones) so a program's existing
    // category still shows up correctly if it was later deactivated.
    supabase.from('program_categories').select('*').order('order_index').then(({ data }) => {
      if (data) setCategories(data);
    });
  }, []);

  const categoryOptions = categories.map(c => ({
    value: c.id,
    label: c.is_active ? c.name : `${c.name} (inactive)`,
    disabled: !c.is_active && c.id !== formData.category_id,
  }));

  async function loadData() {
    const { data } = await supabase
      .from('programs')
      .select('*, program_categories(*)')
      .order('order_index');
    if (data) setPrograms(data as (Program & { program_categories: ProgramCategory | null })[]);
    setLoading(false);
  }

  const filtered = programs.filter(p =>
    !search || p.title.toLowerCase().includes(search.toLowerCase())
  );

  function openCreate() {
    setEditingProgram(null);
    setFormData({ title: '', slug: '', subtitle: '', short_description: '', description: '', thumbnail_url: '', duration_weeks: '', duration_label: '', level: 'beginner', delivery_mode: 'online', price: '', original_price: '', currency: 'KES', category_id: '', is_featured: false, is_published: false });
    setShowForm(true);
  }

  function openEdit(p: Program) {
    setEditingProgram(p);
    setFormData({
      title: p.title, slug: p.slug, subtitle: p.subtitle || '', short_description: p.short_description || '',
      description: p.description || '', thumbnail_url: p.thumbnail_url || '',
      duration_weeks: p.duration_weeks?.toString() || '', duration_label: p.duration_label || '',
      level: p.level, delivery_mode: p.delivery_mode, price: p.price?.toString() || '',
      original_price: p.original_price?.toString() || '', currency: p.currency,
      category_id: p.category_id || '', is_featured: p.is_featured, is_published: p.is_published,
    });
    setShowForm(true);
  }

  async function handleSave() {
    if (!formData.title || !formData.slug) { toast.error('Title and slug are required'); return; }
    setSaving(true);
    const payload = {
      ...formData,
      duration_weeks: formData.duration_weeks ? parseInt(formData.duration_weeks) : null,
      price: formData.price ? parseFloat(formData.price) : null,
      original_price: formData.original_price ? parseFloat(formData.original_price) : null,
      category_id: formData.category_id || null,
    };

    if (editingProgram) {
      const { error } = await supabase.from('programs').update({ ...payload, updated_at: new Date().toISOString() }).eq('id', editingProgram.id);
      if (error) { toast.error(error.message); } else { toast.success('Program updated'); loadData(); setShowForm(false); }
    } else {
      const { error } = await supabase.from('programs').insert(payload);
      if (error) { toast.error(error.message); } else { toast.success('Program created'); loadData(); setShowForm(false); }
    }
    setSaving(false);
  }

  async function togglePublish(p: Program) {
    const { error } = await supabase.from('programs').update({ is_published: !p.is_published }).eq('id', p.id);
    if (!error) { toast.success(p.is_published ? 'Unpublished' : 'Published'); loadData(); }
  }

  async function deleteProgram(id: string) {
    if (!confirm('Delete this program? This cannot be undone.')) return;
    const { error } = await supabase.from('programs').delete().eq('id', id);
    if (error) { toast.error(error.message); } else { toast.success('Program deleted'); loadData(); }
  }

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-black">Programs</h1>
          <p className="text-sm text-muted-foreground">{programs.length} programs</p>
        </div>
        <Button onClick={openCreate} className="bg-brand-500 hover:bg-brand-600 text-white gap-2">
          <Plus className="w-4 h-4" /> Add Program
        </Button>
      </div>

      {/* Search */}
      <div className="relative max-w-sm">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
        <input
          value={search}
          onChange={e => setSearch(e.target.value)}
          placeholder="Search programs..."
          className="w-full pl-9 pr-4 py-2 text-sm bg-card border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500"
        />
      </div>

      {/* Programs table */}
      <div className="bg-card border border-border rounded-2xl overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-border bg-muted/30">
              <th className="text-left px-5 py-3 font-medium text-muted-foreground">Program</th>
              <th className="text-left px-5 py-3 font-medium text-muted-foreground hidden md:table-cell">Category</th>
              <th className="text-left px-5 py-3 font-medium text-muted-foreground">Price</th>
              <th className="text-left px-5 py-3 font-medium text-muted-foreground">Status</th>
              <th className="text-left px-5 py-3 font-medium text-muted-foreground">Actions</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              Array.from({ length: 5 }).map((_, i) => (
                <tr key={i} className="border-b border-border">
                  {Array.from({ length: 5 }).map((_, j) => <td key={j} className="px-5 py-3"><div className="h-4 skeleton rounded w-20" /></td>)}
                </tr>
              ))
            ) : filtered.map(p => (
              <tr key={p.id} className="border-b border-border hover:bg-muted/20 transition-colors">
                <td className="px-5 py-3">
                  <div className="flex items-center gap-3">
                    {p.thumbnail_url && <img src={p.thumbnail_url} alt="" className="w-10 h-8 rounded-lg object-cover" />}
                    <div>
                      <p className="font-medium">{p.title}</p>
                      <p className="text-xs text-muted-foreground">{p.slug}</p>
                    </div>
                  </div>
                </td>
                <td className="px-5 py-3 hidden md:table-cell text-muted-foreground text-xs">
                  {p.program_categories?.name || '—'}
                </td>
                <td className="px-5 py-3 font-medium">
                  {p.price ? `${p.currency} ${p.price.toLocaleString()}` : '—'}
                </td>
                <td className="px-5 py-3">
                  <div className="flex gap-1.5">
                    <span className={cn('text-xs font-medium px-2 py-0.5 rounded-full', p.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600')}>
                      {p.is_published ? 'Published' : 'Draft'}
                    </span>
                    {p.is_featured && <span className="text-xs font-medium px-2 py-0.5 rounded-full bg-orange-100 text-orange-600">Featured</span>}
                  </div>
                </td>
                <td className="px-5 py-3">
                  <div className="flex gap-1.5">
                    <Link href={`/programs/${p.slug}`} target="_blank" className="p-1.5 hover:bg-muted rounded-lg transition-colors text-muted-foreground hover:text-foreground" title="View">
                      <Eye className="w-3.5 h-3.5" />
                    </Link>
                    <button onClick={() => openEdit(p)} className="p-1.5 hover:bg-muted rounded-lg transition-colors text-muted-foreground hover:text-foreground" title="Edit">
                      <Edit className="w-3.5 h-3.5" />
                    </button>
                    <button onClick={() => togglePublish(p)} className="p-1.5 hover:bg-muted rounded-lg transition-colors text-muted-foreground hover:text-foreground" title={p.is_published ? 'Unpublish' : 'Publish'}>
                      {p.is_published ? <ToggleRight className="w-4 h-4 text-green-600" /> : <ToggleLeft className="w-4 h-4" />}
                    </button>
                    <button onClick={() => deleteProgram(p.id)} className="p-1.5 hover:bg-red-50 rounded-lg transition-colors text-muted-foreground hover:text-red-600" title="Delete">
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Form Modal */}
      {showForm && (
        <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
          <div className="bg-background border border-border rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
            <div className="flex items-center justify-between p-5 border-b border-border">
              <h2 className="font-bold text-lg">{editingProgram ? 'Edit Program' : 'New Program'}</h2>
              <button onClick={() => setShowForm(false)} className="p-2 hover:bg-muted rounded-lg">×</button>
            </div>
            <div className="p-5 space-y-4">
              <div className="grid sm:grid-cols-2 gap-4">
                <div>
                  <label className="text-xs font-medium mb-1 block">Title *</label>
                  <input value={formData.title} onChange={e => setFormData(d => ({ ...d, title: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Slug *</label>
                  <input value={formData.slug} onChange={e => setFormData(d => ({ ...d, slug: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div className="sm:col-span-2">
                  <label className="text-xs font-medium mb-1 block">Subtitle</label>
                  <input value={formData.subtitle} onChange={e => setFormData(d => ({ ...d, subtitle: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div className="sm:col-span-2">
                  <label className="text-xs font-medium mb-1 block">Short Description</label>
                  <textarea value={formData.short_description} onChange={e => setFormData(d => ({ ...d, short_description: e.target.value }))} rows={2} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none" />
                </div>
                <div className="sm:col-span-2">
                  <label className="text-xs font-medium mb-1 block">Description</label>
                  <textarea value={formData.description} onChange={e => setFormData(d => ({ ...d, description: e.target.value }))} rows={4} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none" />
                </div>
                <div className="sm:col-span-2">
                  <ImageUpload value={formData.thumbnail_url} onChange={url => setFormData(d => ({ ...d, thumbnail_url: url }))} folder="programs" label="Thumbnail" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Category</label>
                  <select value={formData.category_id} onChange={e => setFormData(d => ({ ...d, category_id: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">No category</option>
                    {categoryOptions.map(c => <option key={c.value} value={c.value} disabled={c.disabled}>{c.label}</option>)}
                  </select>
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Level</label>
                  <select value={formData.level} onChange={e => setFormData(d => ({ ...d, level: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                    <option value="all_levels">All Levels</option>
                  </select>
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Price (KES)</label>
                  <input type="number" value={formData.price} onChange={e => setFormData(d => ({ ...d, price: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Original Price</label>
                  <input type="number" value={formData.original_price} onChange={e => setFormData(d => ({ ...d, original_price: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Duration Label</label>
                  <input value={formData.duration_label} onChange={e => setFormData(d => ({ ...d, duration_label: e.target.value }))} placeholder="6 Weeks" className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Delivery Mode</label>
                  <select value={formData.delivery_mode} onChange={e => setFormData(d => ({ ...d, delivery_mode: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="online">Online</option>
                    <option value="in_person">In-Person</option>
                    <option value="hybrid">Hybrid</option>
                  </select>
                </div>
                <div className="flex items-center gap-4">
                  <label className="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" checked={formData.is_published} onChange={e => setFormData(d => ({ ...d, is_published: e.target.checked }))} className="w-4 h-4 rounded" />
                    <span className="text-sm">Published</span>
                  </label>
                  <label className="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" checked={formData.is_featured} onChange={e => setFormData(d => ({ ...d, is_featured: e.target.checked }))} className="w-4 h-4 rounded" />
                    <span className="text-sm">Featured</span>
                  </label>
                </div>
              </div>

              <div className="flex justify-end gap-3 pt-2 border-t border-border">
                <Button variant="outline" onClick={() => setShowForm(false)}>Cancel</Button>
                <Button onClick={handleSave} disabled={saving} className="bg-brand-500 hover:bg-brand-600 text-white">
                  {saving ? 'Saving...' : (editingProgram ? 'Update Program' : 'Create Program')}
                </Button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
