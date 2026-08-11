'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import Link from 'next/link';
import { format } from 'date-fns';
import { Clock, Search, Tag, ArrowRight, BookOpen } from 'lucide-react';
import type { BlogPost, BlogCategory } from '@/lib/database.types';
import { cn } from '@/lib/utils';

export default function BlogPage() {
  const [posts, setPosts] = useState<BlogPost[]>([]);
  const [categories, setCategories] = useState<BlogCategory[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [selectedCat, setSelectedCat] = useState('');

  useEffect(() => {
    Promise.all([
      supabase.from('blog_posts').select('*').eq('is_published', true).order('published_at', { ascending: false }),
      supabase.from('blog_categories').select('*').eq('is_active', true).order('order_index'),
    ]).then(([postsRes, catsRes]) => {
      if (postsRes.data) setPosts(postsRes.data);
      if (catsRes.data) setCategories(catsRes.data);
      setLoading(false);
    });
  }, []);

  const filtered = posts.filter(p =>
    (!selectedCat || p.category_id === selectedCat) &&
    (!search || p.title.toLowerCase().includes(search.toLowerCase()))
  );
  const featured = filtered.find(p => p.is_featured);
  const rest = filtered.filter(p => !p.is_featured);

  return (
    <>
      <div className="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 text-center">
          <span className="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Blog</span>
          <h1 className="text-4xl sm:text-5xl font-black text-white mb-4">Insights & Stories</h1>
          <p className="text-gray-400 text-lg">Tips, guides, and success stories to help you earn online.</p>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {/* Search & Filter */}
        <div className="flex flex-col sm:flex-row gap-3 mb-8">
          <div className="relative flex-1 max-w-sm">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
            <input
              type="text"
              placeholder="Search articles..."
              value={search}
              onChange={e => setSearch(e.target.value)}
              className="w-full pl-9 pr-4 py-2.5 text-sm bg-card border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500"
            />
          </div>
          <div className="flex gap-2 flex-wrap">
            <button onClick={() => setSelectedCat('')} className={cn('text-sm font-medium px-4 py-2 rounded-full transition-colors', !selectedCat ? 'bg-brand-500 text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80')}>
              All
            </button>
            {categories.map(cat => (
              <button
                key={cat.id}
                onClick={() => setSelectedCat(selectedCat === cat.id ? '' : cat.id)}
                className={cn('text-sm font-medium px-4 py-2 rounded-full transition-colors', selectedCat === cat.id ? 'bg-brand-500 text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80')}
              >
                {cat.name}
              </button>
            ))}
          </div>
        </div>

        {loading ? (
          <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {Array.from({ length: 6 }).map((_, i) => (
              <div key={i} className="bg-card border border-border rounded-2xl overflow-hidden">
                <div className="h-48 skeleton" />
                <div className="p-5 space-y-3">
                  <div className="h-4 skeleton w-2/3 rounded" />
                  <div className="h-3 skeleton w-full rounded" />
                  <div className="h-3 skeleton w-4/5 rounded" />
                </div>
              </div>
            ))}
          </div>
        ) : filtered.length === 0 ? (
          <div className="text-center py-20 text-muted-foreground">
            <BookOpen className="w-12 h-12 mx-auto mb-4 opacity-30" />
            <p className="text-lg font-medium">No articles found</p>
          </div>
        ) : (
          <>
            {featured && (
              <Link href={`/blog/${featured.slug}`} className="block mb-10 group">
                <div className="bg-card border border-border rounded-2xl overflow-hidden card-hover">
                  <div className="grid md:grid-cols-2">
                    {featured.thumbnail_url && (
                      <div className="overflow-hidden">
                        <img src={featured.thumbnail_url} alt={featured.title} className="w-full h-56 md:h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                      </div>
                    )}
                    <div className="p-7 flex flex-col justify-center">
                      <span className="text-xs font-semibold text-brand-600 uppercase tracking-wider mb-3">Featured</span>
                      <h2 className="text-2xl font-black mb-3 group-hover:text-brand-600 transition-colors line-clamp-2">{featured.title}</h2>
                      {featured.excerpt && <p className="text-muted-foreground text-sm mb-4 line-clamp-3">{featured.excerpt}</p>}
                      <div className="flex items-center gap-3 text-xs text-muted-foreground">
                        {featured.published_at && <span>{format(new Date(featured.published_at), 'dd MMM yyyy')}</span>}
                        {featured.read_time_minutes && (
                          <span className="flex items-center gap-1"><Clock className="w-3.5 h-3.5" />{featured.read_time_minutes} min read</span>
                        )}
                      </div>
                      <span className="inline-flex items-center gap-1.5 text-brand-600 font-semibold text-sm mt-4 group-hover:gap-2.5 transition-all">
                        Read Article <ArrowRight className="w-3.5 h-3.5" />
                      </span>
                    </div>
                  </div>
                </div>
              </Link>
            )}

            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {rest.map(post => (
                <Link key={post.id} href={`/blog/${post.slug}`} className="group block">
                  <div className="bg-card border border-border rounded-2xl overflow-hidden card-hover h-full flex flex-col">
                    {post.thumbnail_url && (
                      <div className="overflow-hidden">
                        <img src={post.thumbnail_url} alt={post.title} className="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500" />
                      </div>
                    )}
                    <div className="p-5 flex flex-col flex-1">
                      <h3 className="font-bold line-clamp-2 mb-2 group-hover:text-brand-600 transition-colors">{post.title}</h3>
                      {post.excerpt && <p className="text-sm text-muted-foreground line-clamp-3 mb-4 flex-1">{post.excerpt}</p>}
                      <div className="flex items-center justify-between text-xs text-muted-foreground pt-3 border-t border-border">
                        {post.published_at && <span>{format(new Date(post.published_at), 'dd MMM yyyy')}</span>}
                        {post.read_time_minutes && (
                          <span className="flex items-center gap-1"><Clock className="w-3.5 h-3.5" />{post.read_time_minutes} min</span>
                        )}
                      </div>
                    </div>
                  </div>
                </Link>
              ))}
            </div>
          </>
        )}
      </div>
    </>
  );
}
