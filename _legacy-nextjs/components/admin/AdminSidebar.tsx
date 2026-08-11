'use client';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useAuth } from '@/components/providers/AuthProvider';
import {
  LayoutDashboard, BookOpen, Calendar, Users, UserCheck, Megaphone,
  FileText, Settings, BarChart3, Bell, Shield, Menu as MenuIcon,
  Layers, Star, HelpCircle, Library, ChevronLeft, ChevronRight,
  Zap, LogOut, MousePointerClick, Image, MessageSquare,
  Search, X, TrendingUp
} from 'lucide-react';
import { cn } from '@/lib/utils';

const navGroups = [
  {
    label: 'Overview',
    items: [
      { href: '/admin', label: 'Dashboard', icon: LayoutDashboard, exact: true },
      { href: '/admin/analytics', label: 'Analytics', icon: BarChart3 },
    ],
  },
  {
    label: 'Bookings & Students',
    items: [
      { href: '/admin/bookings', label: 'Bookings', icon: BookOpen },
      { href: '/admin/students', label: 'Students', icon: Users },
      { href: '/admin/cohorts', label: 'Cohorts', icon: Calendar },
    ],
  },
  {
    label: 'Programs',
    items: [
      { href: '/admin/programs', label: 'Programs', icon: Layers },
      { href: '/admin/trainers', label: 'Trainers', icon: UserCheck },
    ],
  },
  {
    label: 'Marketing & CMS',
    items: [
      { href: '/admin/stats', label: 'Stats', icon: TrendingUp },
      { href: '/admin/advertisements', label: 'Advertisements', icon: Megaphone },
      { href: '/admin/ctas', label: 'CTAs', icon: MousePointerClick },
      { href: '/admin/blog', label: 'Blog', icon: FileText },
      { href: '/admin/testimonials', label: 'Testimonials', icon: Star },
      { href: '/admin/faqs', label: 'FAQs', icon: HelpCircle },
      { href: '/admin/resources', label: 'Resources', icon: Library },
    ],
  },
  {
    label: 'Navigation',
    items: [
      { href: '/admin/menus', label: 'Nav Menus', icon: MenuIcon },
    ],
  },
  {
    label: 'System',
    items: [
      { href: '/admin/users', label: 'Users & Roles', icon: Shield },
      { href: '/admin/settings', label: 'Settings', icon: Settings },
    ],
  },
];

interface AdminSidebarProps {
  collapsed: boolean;
  onToggle: () => void;
  mobileOpen: boolean;
  onMobileClose: () => void;
}

export function AdminSidebar({ collapsed, onToggle, mobileOpen, onMobileClose }: AdminSidebarProps) {
  const pathname = usePathname();
  const { signOut, profile } = useAuth();

  const isActive = (href: string, exact?: boolean) =>
    exact ? pathname === href : pathname === href || pathname.startsWith(href + '/');

  // Icon-only layout only applies on desktop; the mobile drawer is always shown expanded.
  const showCollapsed = collapsed && !mobileOpen;

  return (
    <>
      {/* Mobile backdrop */}
      {mobileOpen && (
        <div
          className="fixed inset-0 bg-black/50 z-40 lg:hidden"
          onClick={onMobileClose}
        />
      )}

      <aside className={cn(
        'flex flex-col h-screen w-60 bg-navy-950 text-gray-300 transition-transform duration-300 border-r border-navy-800',
        'fixed inset-y-0 left-0 z-50',
        'lg:sticky lg:top-0 lg:translate-x-0 lg:transition-[width] lg:duration-300',
        collapsed ? 'lg:w-16' : 'lg:w-60',
        mobileOpen ? 'translate-x-0' : '-translate-x-full'
      )}>
      {/* Header */}
      <div className={cn('flex items-center h-16 border-b border-navy-800 px-4', showCollapsed ? 'lg:justify-center' : 'justify-between')}>
        {!showCollapsed && (
          <Link href="/admin" className="flex items-center gap-2" onClick={onMobileClose}>
            <div className="bg-white rounded-lg px-2 py-1.5">
              <img src="/kazilink-logo.png" alt="Kazilink Digital Academy" className="h-8 w-auto" />
            </div>
            <span className="font-bold text-white text-sm">Admin</span>
          </Link>
        )}
        {showCollapsed && (
          <div className="hidden lg:flex w-7 h-7 bg-gradient-to-br from-brand-500 to-navy-600 rounded-lg items-center justify-center">
            <Zap className="w-3.5 h-3.5 text-white" />
          </div>
        )}
        <button
          onClick={onToggle}
          className={cn('hidden lg:block p-1.5 hover:bg-navy-800 rounded-lg transition-colors text-gray-400', showCollapsed && 'lg:hidden')}
        >
          <ChevronLeft className="w-4 h-4" />
        </button>
        <button
          onClick={onMobileClose}
          className="p-1.5 hover:bg-navy-800 rounded-lg transition-colors text-gray-400 lg:hidden"
          aria-label="Close menu"
        >
          <X className="w-4 h-4" />
        </button>
      </div>

      {showCollapsed && (
        <button onClick={onToggle} className="hidden lg:flex justify-center py-2 hover:bg-navy-800 transition-colors">
          <ChevronRight className="w-4 h-4 text-gray-400" />
        </button>
      )}

      {/* Nav */}
      <nav className="flex-1 overflow-y-auto py-4 px-2 space-y-6">
        {navGroups.map(group => (
          <div key={group.label}>
            {!showCollapsed && (
              <p className="text-[10px] font-semibold text-gray-500 uppercase tracking-wider px-2 mb-1.5">{group.label}</p>
            )}
            <div className="space-y-0.5">
              {group.items.map(item => {
                const Icon = item.icon;
                const active = isActive(item.href, item.exact);
                return (
                  <Link
                    key={item.href}
                    href={item.href}
                    title={showCollapsed ? item.label : undefined}
                    onClick={onMobileClose}
                    className={cn(
                      'flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm transition-colors',
                      active
                        ? 'bg-brand-500/15 text-brand-400 font-medium'
                        : 'text-gray-400 hover:bg-navy-800 hover:text-gray-200',
                      showCollapsed && 'lg:justify-center'
                    )}
                  >
                    <Icon className="w-4 h-4 flex-shrink-0" />
                    {!showCollapsed && <span>{item.label}</span>}
                  </Link>
                );
              })}
            </div>
          </div>
        ))}
      </nav>

      {/* Footer */}
      <div className="border-t border-navy-800 p-3 space-y-1">
        <Link href="/" className={cn('flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm text-gray-400 hover:bg-navy-800 transition-colors', showCollapsed && 'justify-center')}>
          <Search className="w-4 h-4" />
          {!showCollapsed && <span>View Website</span>}
        </Link>
        <button
          onClick={() => signOut()}
          className={cn('flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm text-red-400 hover:bg-red-950/30 w-full transition-colors', showCollapsed && 'justify-center')}
        >
          <LogOut className="w-4 h-4" />
          {!showCollapsed && <span>Sign Out</span>}
        </button>
      </div>
      </aside>
    </>
  );
}
