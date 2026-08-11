'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { Download, Search, Filter, FileText, Video, BookOpen, Layout } from 'lucide-react';
import type { Resource } from '@/lib/database.types';
import { cn } from '@/lib/utils';
import { toast } from 'sonner';

const typeIcons: Record<string, React.ReactNode> = {
  pdf: <FileText className="w-5 h-5" />,
  video: <Video className="w-5 h-5" />,
  template: <Layout className="w-5 h-5" />,
  guide: <BookOpen className="w-5 h-5" />,
};

const typeColors: Record<string, string> = {
  pdf: 'bg-red-100 text-red-600',
  video: 'bg-blue-100 text-blue-600',
  template: 'bg-green-100 text-green-600',
  guide: 'bg-orange-100 text-orange-600',
};

export default function ResourcesPage() {
  const [resources, setResources] = useState<Resource[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [type, setType] = useState('');

  useEffect(() => {
    supabase.from('resources').select('*').eq('is_published', true).order('order_index').then(({ data }) => {
      if (data) setResources(data);
      setLoading(false);
    });
  }, []);

  const types = [...new Set(resources.map(r => r.type))];
  const filtered = resources.filter(r =>
    (!type || r.type === type) &&
    (!search || r.title.toLowerCase().includes(search.toLowerCase()))
  );

  async function handleDownload(resource: Resource) {
    if (!resource.file_url) { toast.error('File not available yet'); return; }
    await supabase.from('resources').update({ download_count: resource.download_count + 1 }).eq('id', resource.id);
    window.open(resource.file_url, '_blank');
  }

  return (
    <>
      <div className="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 text-center">
          <span className="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Free Resources</span>
          <h1 className="text-4xl sm:text-5xl font-black text-white mb-4">Learn & Download</h1>
          <p className="text-gray-400 text-lg">Free tools, templates, guides, and training materials.</p>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div className="flex flex-col sm:flex-row gap-3 mb-8">
          <div className="relative flex-1 max-w-sm">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
            <input
              type="text"
              placeholder="Search resources..."
              value={search}
              onChange={e => setSearch(e.target.value)}
              className="w-full pl-9 pr-4 py-2.5 text-sm bg-card border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500"
            />
          </div>
          <div className="flex gap-2">
            <button onClick={() => setType('')} className={cn('text-sm font-medium px-4 py-2 rounded-full transition-colors', !type ? 'bg-brand-500 text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80')}>
              All
            </button>
            {types.map(t => (
              <button key={t} onClick={() => setType(type === t ? '' : t)} className={cn('text-sm font-medium px-4 py-2 rounded-full transition-colors capitalize', type === t ? 'bg-brand-500 text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80')}>
                {t}
              </button>
            ))}
          </div>
        </div>

        {loading ? (
          <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            {Array.from({ length: 6 }).map((_, i) => <div key={i} className="h-40 skeleton rounded-2xl" />)}
          </div>
        ) : filtered.length === 0 ? (
          <div className="text-center py-20 text-muted-foreground">
            <BookOpen className="w-12 h-12 mx-auto mb-4 opacity-30" />
            <p className="text-lg font-medium">No resources found</p>
          </div>
        ) : (
          <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            {filtered.map(r => (
              <div key={r.id} className="bg-card border border-border rounded-2xl p-5 card-hover flex flex-col gap-4">
                <div className="flex items-start gap-3">
                  <div className={cn('w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0', typeColors[r.type] || 'bg-muted text-muted-foreground')}>
                    {typeIcons[r.type] || <BookOpen className="w-5 h-5" />}
                  </div>
                  <div className="flex-1 min-w-0">
                    <h3 className="font-semibold text-sm line-clamp-2">{r.title}</h3>
                    <span className="text-xs text-muted-foreground capitalize">{r.type}</span>
                  </div>
                  {r.is_free && (
                    <span className="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full flex-shrink-0">Free</span>
                  )}
                </div>
                {r.description && <p className="text-xs text-muted-foreground line-clamp-2">{r.description}</p>}
                <div className="flex items-center justify-between pt-2 border-t border-border">
                  <span className="text-xs text-muted-foreground">{r.download_count.toLocaleString()} downloads</span>
                  <button
                    onClick={() => handleDownload(r)}
                    className="flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-700 transition-colors"
                  >
                    <Download className="w-3.5 h-3.5" /> Download
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </>
  );
}
