'use client';
import { useState, useEffect } from 'react';
import Link from 'next/link';
import { supabase } from '@/lib/supabase';
import { Mail, Phone, MapPin, Facebook, Twitter, Instagram, Linkedin, Youtube } from 'lucide-react';
import type { SiteSettingKV, NavItem } from '@/lib/database.types';

const iconMap: Record<string, React.ReactNode> = {
  facebook: <Facebook className="w-4 h-4" />,
  twitter: <Twitter className="w-4 h-4" />,
  instagram: <Instagram className="w-4 h-4" />,
  linkedin: <Linkedin className="w-4 h-4" />,
  youtube: <Youtube className="w-4 h-4" />,
};

export function Footer() {
  const [settings, setSettings] = useState<Record<string, string>>({});
  const [footerItems, setFooterItems] = useState<NavItem[]>([]);

  useEffect(() => {
    async function load() {
      const [settingsRes, menuRes] = await Promise.all([
        supabase.from('site_settings').select('key,value').in('key', [
          'site_name', 'footer_tagline', 'contact_email', 'contact_phone',
          'contact_address', 'social_facebook', 'social_twitter', 'social_instagram',
          'social_linkedin', 'social_youtube',
        ]),
        supabase.from('nav_menus').select('id').eq('location', 'footer').eq('is_active', true).maybeSingle(),
      ]);

      if (settingsRes.data) {
        const map: Record<string, string> = {};
        settingsRes.data.forEach((s: SiteSettingKV) => { if (s.value) map[s.key] = s.value; });
        setSettings(map);
      }

      if (menuRes.data?.id) {
        const { data: items } = await supabase
          .from('nav_items').select('*')
          .eq('menu_id', menuRes.data.id)
          .eq('is_active', true).order('order_index');
        if (items) setFooterItems(items);
      }
    }
    load();
  }, []);

  const year = new Date().getFullYear();

  const quickLinks = [
    { label: 'Programs', url: '/programs' },
    { label: 'Upcoming Cohorts', url: '/cohorts' },
    { label: 'Pricing', url: '/pricing' },
    { label: 'About Us', url: '/about' },
    { label: 'Blog', url: '/blog' },
    { label: 'Resources', url: '/resources' },
    { label: 'FAQ', url: '/faq' },
    { label: 'Contact', url: '/contact' },
  ];

  const programs = [
    { label: 'Freelancing Mastery', url: '/programs/freelancing-mastery-bootcamp' },
    { label: 'E-Commerce Empire', url: '/programs/ecommerce-empire-builder' },
    { label: 'Digital Marketing', url: '/programs/digital-marketing-professional' },
    { label: 'Full-Stack Development', url: '/programs/full-stack-web-development' },
    { label: 'Content Creation', url: '/programs/content-creation-monetization' },
    { label: 'Forex & Crypto Trading', url: '/programs/forex-crypto-trading-mastery' },
  ];

  const socials = [
    { key: 'social_facebook', platform: 'facebook', url: settings.social_facebook },
    { key: 'social_twitter', platform: 'twitter', url: settings.social_twitter },
    { key: 'social_instagram', platform: 'instagram', url: settings.social_instagram },
    { key: 'social_linkedin', platform: 'linkedin', url: settings.social_linkedin },
    { key: 'social_youtube', platform: 'youtube', url: settings.social_youtube },
  ].filter(s => s.url);

  return (
    <footer className="bg-navy-950 text-gray-300">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-8">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
          {/* Brand */}
          <div className="lg:col-span-1">
            <Link href="/" className="flex items-center mb-4 group">
              <div className="bg-white rounded-lg px-2 py-1.5">
                <img src="/kazilink-logo.png" alt={settings.site_name || 'Kazilink Digital Academy'} className="h-10 w-auto" />
              </div>
            </Link>
            <p className="text-sm text-gray-400 leading-relaxed mb-5">
              {settings.footer_tagline || 'Empowering Africans to earn online through practical digital skills training.'}
            </p>
            <div className="space-y-2 text-sm">
              {settings.contact_email && (
                <a href={`mailto:${settings.contact_email}`} className="flex items-center gap-2 text-gray-400 hover:text-brand-400 transition-colors">
                  <Mail className="w-3.5 h-3.5" />{settings.contact_email}
                </a>
              )}
              {settings.contact_phone && (
                <a href={`tel:${settings.contact_phone}`} className="flex items-center gap-2 text-gray-400 hover:text-brand-400 transition-colors">
                  <Phone className="w-3.5 h-3.5" />{settings.contact_phone}
                </a>
              )}
              {settings.contact_address && (
                <div className="flex items-center gap-2 text-gray-400">
                  <MapPin className="w-3.5 h-3.5 flex-shrink-0" />{settings.contact_address}
                </div>
              )}
            </div>
            {socials.length > 0 && (
              <div className="flex gap-3 mt-5">
                {socials.map(s => (
                  <a key={s.platform} href={s.url} target="_blank" rel="noopener noreferrer"
                    className="w-8 h-8 rounded-lg bg-navy-800 hover:bg-brand-600 flex items-center justify-center transition-colors text-gray-400 hover:text-white">
                    {iconMap[s.platform]}
                  </a>
                ))}
              </div>
            )}
          </div>

          {/* Quick Links */}
          <div>
            <h3 className="text-sm font-semibold text-white uppercase tracking-wider mb-4">Quick Links</h3>
            <ul className="space-y-2">
              {quickLinks.map(link => (
                <li key={link.url}>
                  <Link href={link.url} className="text-sm text-gray-400 hover:text-brand-400 transition-colors">
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Programs */}
          <div>
            <h3 className="text-sm font-semibold text-white uppercase tracking-wider mb-4">Programs</h3>
            <ul className="space-y-2">
              {programs.map(p => (
                <li key={p.url}>
                  <Link href={p.url} className="text-sm text-gray-400 hover:text-brand-400 transition-colors">
                    {p.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Newsletter */}
          <div>
            <h3 className="text-sm font-semibold text-white uppercase tracking-wider mb-4">Stay Updated</h3>
            <p className="text-sm text-gray-400 mb-4">Get free income tips, success stories, and new cohort alerts.</p>
            <form className="space-y-2" onSubmit={e => e.preventDefault()}>
              <input
                type="email"
                placeholder="your@email.com"
                className="w-full px-3 py-2.5 text-sm bg-navy-800 border border-navy-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-brand-500 transition-colors"
              />
              <button type="submit" className="w-full bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors">
                Subscribe Free
              </button>
            </form>
            <p className="text-xs text-gray-500 mt-2">No spam. Unsubscribe anytime.</p>
          </div>
        </div>

        <div className="border-t border-navy-800 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
          <p className="text-xs text-gray-500">
            © {year} {settings.site_name || 'Kazilink Digital Academy'}. All rights reserved.
          </p>
          <div className="flex gap-4 text-xs text-gray-500">
            <Link href="/privacy" className="hover:text-brand-400 transition-colors">Privacy Policy</Link>
            <Link href="/terms" className="hover:text-brand-400 transition-colors">Terms of Service</Link>
            <Link href="/refund" className="hover:text-brand-400 transition-colors">Refund Policy</Link>
          </div>
        </div>
      </div>
    </footer>
  );
}
