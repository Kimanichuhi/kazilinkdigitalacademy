'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { toast } from 'sonner';
import { Save, RefreshCw } from 'lucide-react';
import { Button } from '@/components/ui/button';
import type { SiteSettingKV } from '@/lib/database.types';
import { ImageUpload } from '@/components/admin/ImageUpload';

interface SettingGroup {
  label: string;
  category: string;
  settings: { key: string; label: string; type?: string; description?: string }[];
}

const settingGroups: SettingGroup[] = [
  {
    label: 'General',
    category: 'general',
    settings: [
      { key: 'site_name', label: 'Site Name' },
      { key: 'site_tagline', label: 'Tagline' },
      { key: 'site_description', label: 'Description', type: 'textarea' },
    ],
  },
  {
    label: 'Homepage',
    category: 'homepage',
    settings: [
      { key: 'hero_title', label: 'Hero Title' },
      { key: 'hero_subtitle', label: 'Hero Subtitle', type: 'textarea' },
      { key: 'hero_cta_primary', label: 'Primary CTA Text' },
      { key: 'hero_cta_secondary', label: 'Secondary CTA Text' },
      { key: 'hero_image_url', label: 'Hero Image', type: 'image', description: 'Homepage background image' },
    ],
  },
  {
    label: 'Contact',
    category: 'contact',
    settings: [
      { key: 'contact_email', label: 'Contact Email', type: 'email' },
      { key: 'contact_phone', label: 'Phone Number' },
      { key: 'contact_whatsapp', label: 'WhatsApp Number', description: 'Include country code, no spaces (e.g. 254700123456)' },
      { key: 'contact_address', label: 'Office Address' },
    ],
  },
  {
    label: 'Social Media',
    category: 'social',
    settings: [
      { key: 'social_facebook', label: 'Facebook URL', type: 'url' },
      { key: 'social_twitter', label: 'Twitter/X URL', type: 'url' },
      { key: 'social_instagram', label: 'Instagram URL', type: 'url' },
      { key: 'social_linkedin', label: 'LinkedIn URL', type: 'url' },
      { key: 'social_youtube', label: 'YouTube URL', type: 'url' },
      { key: 'social_tiktok', label: 'TikTok URL', type: 'url' },
    ],
  },
  {
    label: 'Branding',
    category: 'branding',
    settings: [
      { key: 'primary_color', label: 'Primary Color', type: 'color' },
      { key: 'secondary_color', label: 'Secondary Color', type: 'color' },
      { key: 'accent_color', label: 'Accent Color', type: 'color' },
    ],
  },
  {
    label: 'Analytics',
    category: 'analytics',
    settings: [
      { key: 'google_analytics_id', label: 'Google Analytics ID', description: 'Your GA4 Measurement ID (G-XXXXXXXXXX)' },
      { key: 'meta_pixel_id', label: 'Meta Pixel ID', description: 'Your Facebook/Meta Pixel ID' },
    ],
  },
  {
    label: 'System',
    category: 'system',
    settings: [
      { key: 'maintenance_mode', label: 'Maintenance Mode', type: 'select', description: 'When enabled, the site shows a maintenance page' },
    ],
  },
];

export default function AdminSettingsPage() {
  const [values, setValues] = useState<Record<string, string>>({});
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [activeGroup, setActiveGroup] = useState('General');

  useEffect(() => {
    supabase.from('site_settings').select('key,value').then(({ data }) => {
      if (data) {
        const map: Record<string, string> = {};
        data.forEach((s: SiteSettingKV) => { map[s.key] = s.value || ''; });
        setValues(map);
      }
      setLoading(false);
    });
  }, []);

  async function handleSave() {
    setSaving(true);
    const allKeys = settingGroups.flatMap(g => g.settings.map(s => s.key));
    const updates = allKeys.map(key => ({
      key,
      value: values[key] || '',
      category: settingGroups.find(g => g.settings.find(s => s.key === key))?.category || 'general',
    }));

    const { error } = await supabase.from('site_settings').upsert(updates, { onConflict: 'key' });
    if (error) { toast.error('Failed to save settings: ' + error.message); }
    else { toast.success('Settings saved successfully!'); }
    setSaving(false);
  }

  const currentGroup = settingGroups.find(g => g.label === activeGroup);

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-black">Settings</h1>
          <p className="text-sm text-muted-foreground">Manage your site configuration</p>
        </div>
        <Button onClick={handleSave} disabled={saving} className="bg-brand-500 hover:bg-brand-600 text-white gap-2">
          {saving ? <RefreshCw className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
          {saving ? 'Saving...' : 'Save Changes'}
        </Button>
      </div>

      <div className="grid lg:grid-cols-4 gap-6">
        {/* Sidebar */}
        <div className="bg-card border border-border rounded-2xl p-2 space-y-0.5 h-fit">
          {settingGroups.map(g => (
            <button
              key={g.label}
              onClick={() => setActiveGroup(g.label)}
              className={`w-full text-left px-3 py-2.5 rounded-xl text-sm font-medium transition-colors ${activeGroup === g.label ? 'bg-brand-500/10 text-brand-600' : 'text-muted-foreground hover:bg-muted'}`}
            >
              {g.label}
            </button>
          ))}
        </div>

        {/* Settings Form */}
        <div className="lg:col-span-3 bg-card border border-border rounded-2xl p-6">
          {loading ? (
            <div className="space-y-4">
              {Array.from({ length: 4 }).map((_, i) => <div key={i} className="h-12 skeleton rounded-xl" />)}
            </div>
          ) : currentGroup ? (
            <div className="space-y-5">
              <h2 className="font-bold text-lg border-b border-border pb-3">{currentGroup.label} Settings</h2>
              {currentGroup.settings.map(setting => (
                <div key={setting.key}>
                  {setting.type !== 'image' && <label className="text-sm font-medium block mb-1.5">{setting.label}</label>}
                  {setting.description && setting.type !== 'image' && (
                    <p className="text-xs text-muted-foreground mb-1.5">{setting.description}</p>
                  )}
                  {setting.type === 'image' ? (
                    <ImageUpload value={values[setting.key] || ''} onChange={url => setValues(v => ({ ...v, [setting.key]: url }))} folder="settings" label={setting.label} />
                  ) : setting.type === 'textarea' ? (
                    <textarea
                      value={values[setting.key] || ''}
                      onChange={e => setValues(v => ({ ...v, [setting.key]: e.target.value }))}
                      rows={3}
                      className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"
                    />
                  ) : setting.type === 'color' ? (
                    <div className="flex gap-3 items-center">
                      <input
                        type="color"
                        value={values[setting.key] || '#000000'}
                        onChange={e => setValues(v => ({ ...v, [setting.key]: e.target.value }))}
                        className="w-10 h-9 border border-border rounded-lg cursor-pointer"
                      />
                      <input
                        value={values[setting.key] || ''}
                        onChange={e => setValues(v => ({ ...v, [setting.key]: e.target.value }))}
                        className="flex-1 px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500"
                      />
                    </div>
                  ) : setting.type === 'select' ? (
                    <select
                      value={values[setting.key] || 'false'}
                      onChange={e => setValues(v => ({ ...v, [setting.key]: e.target.value }))}
                      className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500"
                    >
                      <option value="false">Disabled</option>
                      <option value="true">Enabled</option>
                    </select>
                  ) : (
                    <input
                      type={setting.type || 'text'}
                      value={values[setting.key] || ''}
                      onChange={e => setValues(v => ({ ...v, [setting.key]: e.target.value }))}
                      className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500"
                    />
                  )}
                </div>
              ))}
            </div>
          ) : null}
        </div>
      </div>
    </div>
  );
}
