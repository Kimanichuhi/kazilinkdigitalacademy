'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { toast } from 'sonner';
import { Plus, Edit, Trash2, Eye, ToggleLeft, ToggleRight } from 'lucide-react';
import type { BlogPost, BlogCategory, Profile } from '@/lib/database.types';
import { Button } from '@/components/ui/button';
import { format } from 'date-fns';
import { cn } from '@/lib/utils';
import { ImageUpload } from '@/components/admin/ImageUpload';

export default function AdminBlogPage() {
  const [posts, setPosts] = useState<BlogPost[]>([]);
  const [categories, setCategories] = useState<BlogCategory[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState<BlogPost | null>(null);
  const [saving, setSaving] = useState(false);
  const [formData, setFormData] = useState({
    title: '', slug: '', excerpt: '', content: '', thumbnail_url: '',
    category_id: '', is_featured: false, is_published: false,
    seo_title: '', seo_description: '', read_time_minutes: '5',
    tags: '',
  });

  useEffect(() => {
    load();
    // Fetch every category (not just active ones) so a post's existing
    // category still shows up correctly if it was later deactivated.
    supabase.from('blog_categories').select('*').then(({ data }) => { if (data) setCategories(data); });
  }, []);

  const categoryOptions = categories.map(c => ({
    value: c.id,
    label: c.is_active ? c.name : `${c.name} (inactive)`,
    disabled: !c.is_active && c.id !== formData.category_id,
  }));

  async function load() {
    const { data } = await supabase.from('blog_posts').select('*').order('created_at', { ascending: false });
    if (data) setPosts(data);
    setLoading(false);
  }

  function openEdit(p: BlogPost) {
    setEditing(p);
    setFormData({
      title: p.title, slug: p.slug, excerpt: p.excerpt || '', content: p.content || '',
      thumbnail_url: p.thumbnail_url || '', category_id: p.category_id || '',
      is_featured: p.is_featured, is_published: p.is_published,
      seo_title: p.seo_title || '', seo_description: p.seo_description || '',
      read_time_minutes: p.read_time_minutes?.toString() || '5',
      tags: p.tags?.join(', ') || '',
    });
    setShowForm(true);
  }

  async function handleSave() {
    if (!formData.title || !formData.slug) { toast.error('Title and slug required'); return; }
    setSaving(true);
    const payload = {
      ...formData,
      read_time_minutes: parseInt(formData.read_time_minutes) || 5,
      tags: formData.tags ? formData.tags.split(',').map(t => t.trim()).filter(Boolean) : null,
      category_id: formData.category_id || null,
      published_at: formData.is_published ? new Date().toISOString() : null,
    };
    const op = editing
      ? supabase.from('blog_posts').update({ ...payload, updated_at: new Date().toISOString() }).eq('id', editing.id)
      : supabase.from('blog_posts').insert(payload as any);
    const { error } = await op;
    if (error) { toast.error(error.message); } else { toast.success(editing ? 'Post updated' : 'Post created'); load(); setShowForm(false); }
    setSaving(false);
  }

  async function togglePublish(p: BlogPost) {
    await supabase.from('blog_posts').update({ is_published: !p.is_published, published_at: !p.is_published ? new Date().toISOString() : null }).eq('id', p.id);
    toast.success(p.is_published ? 'Unpublished' : 'Published'); load();
  }

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <div><h1 className="text-2xl font-black">Blog</h1><p className="text-sm text-muted-foreground">{posts.length} posts</p></div>
        <Button onClick={() => { setEditing(null); setFormData({ title: '', slug: '', excerpt: '', content: '', thumbnail_url: '', category_id: '', is_featured: false, is_published: false, seo_title: '', seo_description: '', read_time_minutes: '5', tags: '' }); setShowForm(true); }} className="bg-brand-500 hover:bg-brand-600 text-white gap-2">
          <Plus className="w-4 h-4" /> New Post
        </Button>
      </div>

      <div className="bg-card border border-border rounded-2xl overflow-hidden">
        <table className="w-full text-sm">
          <thead><tr className="border-b border-border bg-muted/30">
            <th className="text-left px-5 py-3 font-medium text-muted-foreground">Title</th>
            <th className="text-left px-5 py-3 font-medium text-muted-foreground hidden md:table-cell">Category</th>
            <th className="text-left px-5 py-3 font-medium text-muted-foreground">Status</th>
            <th className="text-left px-5 py-3 font-medium text-muted-foreground hidden lg:table-cell">Date</th>
            <th className="text-left px-5 py-3 font-medium text-muted-foreground">Actions</th>
          </tr></thead>
          <tbody>
            {loading ? Array.from({ length: 5 }).map((_, i) => <tr key={i} className="border-b"><td colSpan={5} className="px-5 py-3"><div className="h-4 skeleton rounded" /></td></tr>) :
              posts.map(p => (
                <tr key={p.id} className="border-b border-border hover:bg-muted/20 transition-colors">
                  <td className="px-5 py-3">
                    <div className="flex items-center gap-3">
                      {p.thumbnail_url && <img src={p.thumbnail_url} alt="" className="w-10 h-8 rounded object-cover" />}
                      <div>
                        <p className="font-medium line-clamp-1">{p.title}</p>
                        <p className="text-xs text-muted-foreground">{p.slug}</p>
                      </div>
                    </div>
                  </td>
                  <td className="px-5 py-3 hidden md:table-cell text-xs text-muted-foreground">—</td>
                  <td className="px-5 py-3">
                    <div className="flex gap-1">
                      <span className={cn('text-xs font-medium px-2 py-0.5 rounded-full', p.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600')}>
                        {p.is_published ? 'Published' : 'Draft'}
                      </span>
                      {p.is_featured && <span className="text-xs font-medium px-2 py-0.5 rounded-full bg-orange-100 text-orange-600">Featured</span>}
                    </div>
                  </td>
                  <td className="px-5 py-3 hidden lg:table-cell text-xs text-muted-foreground">
                    {p.published_at ? format(new Date(p.published_at), 'dd MMM yyyy') : '—'}
                  </td>
                  <td className="px-5 py-3">
                    <div className="flex gap-1.5">
                      <button onClick={() => openEdit(p)} className="p-1.5 hover:bg-muted rounded-lg"><Edit className="w-3.5 h-3.5 text-muted-foreground" /></button>
                      <button onClick={() => togglePublish(p)} className="p-1.5 hover:bg-muted rounded-lg">
                        {p.is_published ? <ToggleRight className="w-4 h-4 text-green-600" /> : <ToggleLeft className="w-4 h-4 text-muted-foreground" />}
                      </button>
                      <button onClick={async () => { if (!confirm('Delete?')) return; await supabase.from('blog_posts').delete().eq('id', p.id); toast.success('Deleted'); load(); }} className="p-1.5 hover:bg-red-50 rounded-lg"><Trash2 className="w-3.5 h-3.5 text-muted-foreground" /></button>
                    </div>
                  </td>
                </tr>
              ))}
          </tbody>
        </table>
      </div>

      {showForm && (
        <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
          <div className="bg-background border border-border rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
            <div className="flex items-center justify-between p-5 border-b border-border">
              <h2 className="font-bold">{editing ? 'Edit Post' : 'New Post'}</h2>
              <button onClick={() => setShowForm(false)} className="p-2 hover:bg-muted rounded-lg">×</button>
            </div>
            <div className="p-5 space-y-4">
              <div className="grid sm:grid-cols-2 gap-4">
                <div className="sm:col-span-2"><label className="text-xs font-medium mb-1 block">Title *</label><input value={formData.title} onChange={e => setFormData(d => ({ ...d, title: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" /></div>
                <div><label className="text-xs font-medium mb-1 block">Slug *</label><input value={formData.slug} onChange={e => setFormData(d => ({ ...d, slug: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" /></div>
                <div><label className="text-xs font-medium mb-1 block">Category</label><select value={formData.category_id} onChange={e => setFormData(d => ({ ...d, category_id: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500"><option value="">No category</option>{categoryOptions.map(c => <option key={c.value} value={c.value} disabled={c.disabled}>{c.label}</option>)}</select></div>
                <div className="sm:col-span-2"><label className="text-xs font-medium mb-1 block">Excerpt</label><textarea value={formData.excerpt} onChange={e => setFormData(d => ({ ...d, excerpt: e.target.value }))} rows={2} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none" /></div>
                <div className="sm:col-span-2"><label className="text-xs font-medium mb-1 block">Content</label><textarea value={formData.content} onChange={e => setFormData(d => ({ ...d, content: e.target.value }))} rows={8} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none" /></div>
                <div className="sm:col-span-2"><ImageUpload value={formData.thumbnail_url} onChange={url => setFormData(d => ({ ...d, thumbnail_url: url }))} folder="blog" label="Thumbnail" /></div>
                <div><label className="text-xs font-medium mb-1 block">Tags (comma-separated)</label><input value={formData.tags} onChange={e => setFormData(d => ({ ...d, tags: e.target.value }))} placeholder="freelancing, kenya, tips" className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" /></div>
                <div><label className="text-xs font-medium mb-1 block">Read Time (minutes)</label><input type="number" value={formData.read_time_minutes} onChange={e => setFormData(d => ({ ...d, read_time_minutes: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" /></div>
                <div className="flex items-center gap-4">
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
