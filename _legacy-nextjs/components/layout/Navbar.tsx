'use client';
import { useState, useEffect } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { supabase } from '@/lib/supabase';
import { useAuth } from '@/components/providers/AuthProvider';
import { useTheme } from 'next-themes';
import { Button } from '@/components/ui/button';
import {
  Menu, X, Moon, Sun, ChevronDown, Bell, LogOut, User, LayoutDashboard
} from 'lucide-react';
import { cn } from '@/lib/utils';
import type { NavItem, SiteSettingKV } from '@/lib/database.types';

interface NavItemWithChildren extends NavItem {
  children?: NavItemWithChildren[];
}

export function Navbar() {
  const [mobileOpen, setMobileOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const [navItems, setNavItems] = useState<NavItemWithChildren[]>([]);
  const [siteName, setSiteName] = useState('Kazilink Digital Academy');
  const [announcement, setAnnouncement] = useState<string | null>(null);
  const [userMenuOpen, setUserMenuOpen] = useState(false);
  const pathname = usePathname();
  const { theme, setTheme } = useTheme();
  const { user, profile, signOut } = useAuth();
  const [mounted, setMounted] = useState(false);

  useEffect(() => { setMounted(true); }, []);

  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 20);
    window.addEventListener('scroll', handleScroll, { passive: true });
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  useEffect(() => {
    async function loadNav() {
      const [menuRes, settingsRes, adRes] = await Promise.all([
        supabase.from('nav_menus').select('id').eq('location', 'header').eq('is_active', true).maybeSingle(),
        supabase.from('site_settings').select('key,value').in('key', ['site_name']),
        supabase.from('advertisements').select('title').eq('type', 'announcement_bar').eq('status', 'active').order('priority', { ascending: false }).limit(1).maybeSingle(),
      ]);

      if (settingsRes.data) {
        const nameRow = settingsRes.data.find((s: SiteSettingKV) => s.key === 'site_name');
        if (nameRow?.value) setSiteName(nameRow.value);
      }
      if (adRes.data?.title) setAnnouncement(adRes.data.title);

      if (menuRes.data?.id) {
        const { data: items } = await supabase
          .from('nav_items')
          .select('*')
          .eq('menu_id', menuRes.data.id)
          .eq('is_active', true)
          .order('order_index');

        if (items) {
          const topLevel = items.filter((i: NavItem) => !i.parent_id);
          const nested = topLevel.map((item: NavItem) => ({
            ...item,
            children: items.filter((c: NavItem) => c.parent_id === item.id),
          }));
          setNavItems(nested);
        }
      }
    }
    loadNav();
  }, []);

  const isActive = (url: string | null) => url === pathname;

  return (
    <>
      {/* Announcement bar */}
      {announcement && (
        <div className="announcement-bar text-white text-center py-2 text-sm font-medium px-4">
          <span>{announcement}</span>
          <Link href="/booking" className="ml-3 underline font-bold hover:no-underline">
            Enroll Now →
          </Link>
        </div>
      )}

      <nav className={cn(
        'sticky top-0 z-50 w-full transition-all duration-300',
        scrolled
          ? 'bg-white/95 dark:bg-gray-950/95 backdrop-blur-lg shadow-sm border-b border-border'
          : 'bg-white dark:bg-gray-950'
      )}>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-16">
            {/* Logo */}
            <Link href="/" className="flex items-center group">
              <div className="dark:bg-white dark:rounded-lg dark:px-2 dark:py-1.5">
                <img src="/kazilink-logo.png" alt={siteName} className="h-14 w-auto" />
              </div>
            </Link>

            {/* Desktop nav */}
            <div className="hidden lg:flex items-center gap-1">
              {navItems.map((item) => (
                <div key={item.id} className="relative group">
                  <Link
                    href={item.url || '#'}
                    className={cn(
                      'flex items-center gap-1 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                      isActive(item.url)
                        ? 'text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-950'
                        : 'text-gray-600 dark:text-gray-300 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-gray-50 dark:hover:bg-gray-800'
                    )}
                  >
                    {item.label}
                    {item.children && item.children.length > 0 && (
                      <ChevronDown className="w-3.5 h-3.5" />
                    )}
                    {item.badge && (
                      <span className="ml-1 bg-orange-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                        {item.badge}
                      </span>
                    )}
                  </Link>
                  {item.children && item.children.length > 0 && (
                    <div className="absolute top-full left-0 mt-1 w-52 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 p-1">
                      {item.children.map((child) => (
                        <Link
                          key={child.id}
                          href={child.url || '#'}
                          className="block px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-950 rounded-lg transition-colors"
                        >
                          {child.label}
                        </Link>
                      ))}
                    </div>
                  )}
                </div>
              ))}
            </div>

            {/* Right actions */}
            <div className="flex items-center gap-2">
              {mounted && (
                <button
                  onClick={() => setTheme(theme === 'dark' ? 'light' : 'dark')}
                  className="p-2 rounded-lg text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                  aria-label="Toggle theme"
                >
                  {theme === 'dark' ? <Sun className="w-4 h-4" /> : <Moon className="w-4 h-4" />}
                </button>
              )}

              {user ? (
                <div className="relative">
                  <button
                    onClick={() => setUserMenuOpen(!userMenuOpen)}
                    className="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                  >
                    <div className="w-7 h-7 rounded-full bg-brand-500 flex items-center justify-center text-white text-xs font-bold">
                      {profile?.full_name?.[0] || user.email?.[0]?.toUpperCase() || 'U'}
                    </div>
                    <ChevronDown className="w-3.5 h-3.5 text-gray-500" />
                  </button>
                  {userMenuOpen && (
                    <div className="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-border z-50 p-1">
                      <div className="px-3 py-2 border-b border-border mb-1">
                        <p className="text-sm font-medium truncate">{profile?.full_name || 'User'}</p>
                        <p className="text-xs text-muted-foreground truncate">{user.email}</p>
                      </div>
                      {profile?.role && ['admin', 'super_admin', 'content_manager', 'marketing', 'finance', 'support'].includes(profile.role) && (
                        <Link href="/admin" onClick={() => setUserMenuOpen(false)} className="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg">
                          <LayoutDashboard className="w-4 h-4" /> Admin Dashboard
                        </Link>
                      )}
                      <Link href="/student" onClick={() => setUserMenuOpen(false)} className="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg">
                        <User className="w-4 h-4" /> My Dashboard
                      </Link>
                      <Link href="/student/notifications" onClick={() => setUserMenuOpen(false)} className="flex items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg">
                        <Bell className="w-4 h-4" /> Notifications
                      </Link>
                      <hr className="my-1 border-border" />
                      <button
                        onClick={() => { signOut(); setUserMenuOpen(false); }}
                        className="flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-950 rounded-lg w-full"
                      >
                        <LogOut className="w-4 h-4" /> Sign Out
                      </button>
                    </div>
                  )}
                </div>
              ) : (
                <div className="hidden sm:flex items-center gap-2">
                  <Link href="/auth/login">
                    <Button variant="ghost" size="sm" className="text-sm">Log In</Button>
                  </Link>
                  <Link href="/booking">
                    <Button size="sm" className="bg-brand-500 hover:bg-brand-600 text-white text-sm shadow-lg shadow-brand-500/25">
                      Enroll Now
                    </Button>
                  </Link>
                </div>
              )}

              {/* Mobile menu button */}
              <button
                onClick={() => setMobileOpen(!mobileOpen)}
                className="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
              >
                {mobileOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
              </button>
            </div>
          </div>
        </div>

        {/* Mobile menu */}
        {mobileOpen && (
          <div className="lg:hidden border-t border-border bg-white dark:bg-gray-950 px-4 py-4 space-y-1">
            {navItems.map((item) => (
              <Link
                key={item.id}
                href={item.url || '#'}
                onClick={() => setMobileOpen(false)}
                className={cn(
                  'block px-3 py-2.5 rounded-lg text-sm font-medium',
                  isActive(item.url)
                    ? 'bg-brand-50 dark:bg-brand-950 text-brand-600 dark:text-brand-400'
                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'
                )}
              >
                {item.label}
              </Link>
            ))}
            <div className="pt-3 border-t border-border flex flex-col gap-2">
              {!user && (
                <>
                  <Link href="/auth/login" onClick={() => setMobileOpen(false)}>
                    <Button variant="outline" className="w-full">Log In</Button>
                  </Link>
                  <Link href="/booking" onClick={() => setMobileOpen(false)}>
                    <Button className="w-full bg-brand-500 hover:bg-brand-600 text-white">Enroll Now</Button>
                  </Link>
                </>
              )}
            </div>
          </div>
        )}
      </nav>
    </>
  );
}
