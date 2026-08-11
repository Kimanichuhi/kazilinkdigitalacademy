'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { toast } from 'sonner';
import {
  Plus, Edit, Trash2, Eye, EyeOff, Sparkles, Loader2, Copy,
  RefreshCw, Wand2, Image as ImageIcon, Calendar, Megaphone
} from 'lucide-react';
import type { Advertisement } from '@/lib/database.types';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { format } from 'date-fns';
import { ImageUpload } from '@/components/admin/ImageUpload';

const placements = ['homepage', 'about', 'programs', 'pricing', 'booking', 'blog', 'contact', 'footer', 'sidebar', 'popup'];
const types = ['hero_banner', 'popup', 'sidebar_banner', 'floating_banner', 'announcement_bar', 'carousel', 'video', 'image', 'sponsored_section', 'countdown_banner'];

const statusColors: Record<string, string> = {
  draft: 'bg-gray-100 text-gray-600',
  active: 'bg-green-100 text-green-700',
  paused: 'bg-yellow-100 text-yellow-700',
  archived: 'bg-gray-100 text-gray-500',
  expired: 'bg-red-100 text-red-600',
};

// ── AI Ad Copy Generator ──────────────────────────────────────────
// Uses marketing copywriting formulas (AIDA, PAS) to generate
// compelling ad copy from the campaign name and context.

const urgencyWords = ['Now', 'Today', 'Limited', 'Closing Soon', 'Final Chance', 'Hurry'];
const benefitWords = ['Boost', 'Master', 'Transform', 'Skyrocket', 'Unlock', 'Accelerate', 'Launch'];
const actionWords = ['Join', 'Start', 'Claim', 'Book', 'Reserve', 'Secure', 'Enroll'];

function pick<T>(arr: T[], seed: number): T {
  return arr[seed % arr.length];
}

function generateAdCopy(campaignName: string, adType: string, seed: number) {
  const name = campaignName.trim();
  const lower = name.toLowerCase();

  // Detect context from campaign name
  const isEvent = /webinar|event|workshop|masterclass|live|summit|conference/.test(lower);
  const isSale = /sale|discount|offer|deal|flash|promo|black friday/.test(lower);
  const isCohort = /cohort|intake|batch|class|enrollment|registration/.test(lower);
  const isCourse = /course|program|training|bootcamp|certification/.test(lower);
  const isFree = /free|gratis|complimentary/.test(lower);

  // Generate title
  let titles: string[] = [];
  if (isEvent) {
    titles = [
      `${name}: Don't Miss Out!`,
      `Join ${name} — Live & Free`,
      `${name} is Here. Are You Ready?`,
      `Be Part of ${name}`,
      `${name}: Reserve Your Free Seat`,
    ];
  } else if (isSale) {
    titles = [
      `${name} — Save Big Today!`,
      `Flash: ${name} Ends Soon`,
      `${name} — Don't Miss This Deal`,
      `Limited Time: ${name}`,
      `${name} — Your Best Price Awaits`,
    ];
  } else if (isCohort) {
    titles = [
      `${name} — Seats Filling Fast`,
      `Secure Your Spot in ${name}`,
      `${name} — Enrollment Now Open`,
      `Join ${name} Before It's Full`,
      `${name}: Limited Seats Available`,
    ];
  } else if (isFree) {
    titles = [
      `Free: ${name}`,
      `${name} — 100% Free Access`,
      `Get ${name} at No Cost`,
      `${name} — Free for Everyone`,
      `Claim Your Free ${name}`,
    ];
  } else {
    titles = [
      `${name} — Start Your Journey`,
      `${pick(benefitWords, seed)} Your Skills with ${name}`,
      `${name}: Your Path to Success`,
      `Unlock ${name} Today`,
      `${name} — Don't Wait, Start Now`,
    ];
  }

  // Generate subtitle
  let subtitles: string[] = [];
  if (isEvent) {
    subtitles = [
      'Live Session — Register Now',
      'Exclusive Online Event',
      'Limited Seats Available',
      'Join Us Live on Zoom',
      `One Day Only — Don't Miss It`,
    ];
  } else if (isSale) {
    subtitles = [
      `${pick(['20%', '30%', '50%', 'Special'], seed)} Off — Limited Time`,
      'Flash Sale Ends Soon',
      'Biggest Discount of the Season',
      'Save More This Week Only',
      'Exclusive Pricing — Act Fast',
    ];
  } else if (isCohort) {
    subtitles = [
      'Early Bird Pricing Available',
      'Limited Seats — Book Early',
      'Next Batch Starting Soon',
      'Reserve Your Seat Today',
      'Enrollment Closes Soon',
    ];
  } else if (isFree) {
    subtitles = [
      'No Cost, No Catch',
      '100% Free — No Hidden Fees',
      'Limited Free Spots Available',
      'Register Free While Slots Last',
      'Free Access for First 100 Signups',
    ];
  } else {
    subtitles = [
      'Transform Your Future Today',
      'Practical Skills, Real Results',
      'Learn From Industry Experts',
      'Start Earning Online Faster',
      'Your Career Upgrade Starts Here',
    ];
  }

  // Generate description using AIDA formula
  let descriptions: string[] = [];
  const benefit = pick(benefitWords, seed).toLowerCase();
  const action = pick(actionWords, seed + 2).toLowerCase();
  const urgency = pick(urgencyWords, seed + 4).toLowerCase();

  if (isEvent) {
    descriptions = [
      `Attention all aspiring digital professionals! ${name} is happening soon and you're invited. This is your chance to learn directly from industry experts, ask your questions live, and walk away with actionable strategies you can use immediately. ${pick(actionWords, seed + 1)} now — seats are limited and filling fast!`,
      `Don't miss ${name}! Join hundreds of ambitious learners who are leveling up their skills and income. This live session is packed with real-world insights, proven frameworks, and insider tips you won't find anywhere else. Register today to secure your free spot.`,
      `Ready to ${benefit} your income and career? ${name} brings you face-to-face with top trainers who've helped thousands succeed online. Get practical tips, live Q&A, and a step-by-step action plan. ${urgency} — this opportunity won't last!`,
    ];
  } else if (isSale) {
    descriptions = [
      `For a limited time only, get ${name} at an unbeatable price! Whether you're starting fresh or leveling up, this is your moment to invest in yourself without breaking the bank. ${pick(actionWords, seed + 1)} this offer before it's gone — flash sales don't last forever!`,
      `Why pay more? ${name} is now available at our lowest price of the year. Join thousands of students who've transformed their lives through our training programs. This flash deal ends soon, so act now and start your journey today.`,
      `Your future deserves the best — and right now, the best comes at a discount. ${name} gives you everything you need to ${benefit} your skills and income. Don't wait — grab this deal ${urgency} before the timer runs out!`,
    ];
  } else if (isCohort) {
    descriptions = [
      `${name} is now open for enrollment! This is your opportunity to join a structured, guided learning experience with expert trainers and a community of like-minded peers. Seats are strictly limited to ensure quality — ${action} your spot today and start your transformation.`,
      `Ready to take the leap? ${name} is designed to take you from where you are to where you want to be. With hands-on projects, personalized feedback, and real-world assignments, you'll graduate ready to earn. Book your seat before they're all gone!`,
      `The ${name} is filling up fast! Don't be the one who missed out. Our cohorts sell out because they deliver results — our graduates are earning online within weeks of completion. ${pick(actionWords, seed + 3)} your seat ${urgency} and join the next generation of digital earners.`,
    ];
  } else if (isFree) {
    descriptions = [
      `Yes, ${name} is completely free! We believe everyone deserves access to quality digital skills training. This free offering gives you a taste of what our full programs can do. No credit card, no catch — just real value. Sign up ${urgency} while free spots are still available!`,
      `Why are we giving ${name} away for free? Because we've seen how the right training can change lives. This is your no-risk opportunity to learn something new, ${benefit} your income potential, and join our growing community. Grab your free spot today — nothing to lose, everything to gain.`,
    ];
  } else {
    descriptions = [
      `${name} is your gateway to mastering the digital skills that pay. Designed by industry experts who've been there and done it, this program gives you the exact blueprint to ${benefit} your income online. No fluff, no theory — just practical, proven strategies. ${pick(actionWords, seed + 1)} today and start building your future!`,
      `What if you could ${benefit} your income in just a few weeks? ${name} makes it possible. Step by step, our expert trainers walk you through everything you need to know to start earning online. Join thousands of graduates who are already making it happen. Your journey starts here.`,
      `Stop dreaming. Start doing. ${name} gives you the skills, the community, and the support to turn your ambitions into real income. Whether you're a beginner or looking to level up, this is your moment. ${urgency} — your future self will thank you.`,
    ];
  }

  // Generate CTA text
  let ctaTexts: string[] = [];
  if (isEvent) {
    ctaTexts = ['Register Free', 'Save My Seat', 'Join Live', 'Reserve Spot', 'Get Access'];
  } else if (isSale) {
    ctaTexts = ['Claim Discount', 'Get Deal', 'Shop Now', 'Save Today', 'Grab Offer'];
  } else if (isCohort) {
    ctaTexts = ['Book My Spot', 'Enroll Now', 'Reserve Seat', 'Join Cohort', 'Secure Spot'];
  } else if (isFree) {
    ctaTexts = ['Get It Free', 'Claim Free Spot', 'Start Free', 'Access Now', 'Join Free'];
  } else {
    ctaTexts = ['Start Now', 'Enroll Today', 'Get Started', 'Join Now', 'Learn More'];
  }

  return {
    title: pick(titles, seed),
    subtitle: pick(subtitles, seed + 1),
    description: pick(descriptions, seed + 2),
    cta_text: pick(ctaTexts, seed + 3),
  };
}

function generateVariations(campaignName: string, adType: string, count: number) {
  return Array.from({ length: count }, (_, i) => generateAdCopy(campaignName, adType, i));
}

// ── Component ────────────────────────────────────────────────────

export default function AdminAdvertisementsPage() {
  const [ads, setAds] = useState<Advertisement[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState<Advertisement | null>(null);
  const [saving, setSaving] = useState(false);
  const [formData, setFormData] = useState({
    campaign_name: '', title: '', subtitle: '', description: '',
    type: 'hero_banner', placement: ['homepage'] as string[],
    desktop_image_url: '', cta_text: '', cta_link: '',
    background_color: '#0c4a6e', priority: '0', status: 'draft',
    publish_date: '', expiry_date: '',
  });

  // AI generation state
  const [generating, setGenerating] = useState(false);
  const [aiVariations, setAiVariations] = useState<{ title: string; subtitle: string; description: string; cta_text: string }[]>([]);
  const [selectedVariation, setSelectedVariation] = useState(-1);

  useEffect(() => { loadAds(); }, []);

  async function loadAds() {
    const { data } = await supabase.from('advertisements').select('*').order('priority', { ascending: false });
    if (data) setAds(data);
    setLoading(false);
  }

  function openCreate() {
    setEditing(null);
    setFormData({ campaign_name: '', title: '', subtitle: '', description: '', type: 'hero_banner', placement: ['homepage'], desktop_image_url: '', cta_text: '', cta_link: '', background_color: '#0c4a6e', priority: '0', status: 'draft', publish_date: '', expiry_date: '' });
    setAiVariations([]);
    setSelectedVariation(-1);
    setShowForm(true);
  }

  function openEdit(ad: Advertisement) {
    setEditing(ad);
    setFormData({
      campaign_name: ad.campaign_name, title: ad.title || '', subtitle: ad.subtitle || '',
      description: ad.description || '', type: ad.type, placement: ad.placement,
      desktop_image_url: ad.desktop_image_url || '', cta_text: ad.cta_text || '',
      cta_link: ad.cta_link || '', background_color: ad.background_color || '#0c4a6e',
      priority: ad.priority.toString(), status: ad.status,
      publish_date: ad.publish_date ? ad.publish_date.slice(0, 16) : '',
      expiry_date: ad.expiry_date ? ad.expiry_date.slice(0, 16) : '',
    });
    setAiVariations([]);
    setSelectedVariation(-1);
    setShowForm(true);
  }

  function handleGenerate() {
    if (!formData.campaign_name.trim()) { toast.error('Enter a campaign name first'); return; }
    setGenerating(true);
    // Simulate AI thinking time for UX
    setTimeout(() => {
      const variations = generateVariations(formData.campaign_name, formData.type, 3);
      setAiVariations(variations);
      setGenerating(false);
      toast.success('Generated 3 ad copy variations');
    }, 800);
  }

  function applyVariation(idx: number) {
    const v = aiVariations[idx];
    setFormData(d => ({ ...d, title: v.title, subtitle: v.subtitle, description: v.description, cta_text: v.cta_text }));
    setSelectedVariation(idx);
    toast.success('Ad copy applied to form');
  }

  async function handleSave() {
    if (!formData.campaign_name) { toast.error('Campaign name required'); return; }
    setSaving(true);
    const payload = {
      ...formData,
      priority: parseInt(formData.priority) || 0,
      publish_date: formData.publish_date || null,
      expiry_date: formData.expiry_date || null,
    };

    const op = editing
      ? supabase.from('advertisements').update({ ...payload, updated_at: new Date().toISOString() }).eq('id', editing.id)
      : supabase.from('advertisements').insert(payload as any);

    const { error } = await op;
    if (error) { toast.error(error.message); } else {
      toast.success(editing ? 'Ad updated' : 'Ad created');
      loadAds();
      setShowForm(false);
    }
    setSaving(false);
  }

  async function toggleStatus(ad: Advertisement) {
    const newStatus = ad.status === 'active' ? 'paused' : 'active';
    const { error } = await supabase.from('advertisements').update({ status: newStatus }).eq('id', ad.id);
    if (!error) { toast.success(`Ad ${newStatus}`); loadAds(); }
  }

  async function deleteAd(id: string) {
    if (!confirm('Delete this advertisement?')) return;
    const { error } = await supabase.from('advertisements').delete().eq('id', id);
    if (!error) { toast.success('Deleted'); loadAds(); }
  }

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-black flex items-center gap-2">
            <Megaphone className="w-6 h-6 text-brand-500" />
            Advertisements
          </h1>
          <p className="text-sm text-muted-foreground">{ads.length} campaigns · {ads.filter(a => a.status === 'active').length} active</p>
        </div>
        <Button onClick={openCreate} className="bg-brand-500 hover:bg-brand-600 text-white gap-2">
          <Plus className="w-4 h-4" /> New Ad
        </Button>
      </div>

      {/* Stats Row */}
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
        {[
          { label: 'Active', value: ads.filter(a => a.status === 'active').length, color: 'text-green-600' },
          { label: 'Draft', value: ads.filter(a => a.status === 'draft').length, color: 'text-gray-500' },
          { label: 'Total Views', value: ads.reduce((s, a) => s + a.view_count, 0), color: 'text-brand-600' },
          { label: 'Total Clicks', value: ads.reduce((s, a) => s + a.click_count, 0), color: 'text-blue-600' },
        ].map(s => (
          <div key={s.label} className="bg-card border border-border rounded-xl p-4">
            <p className="text-xs text-muted-foreground mb-1">{s.label}</p>
            <p className={cn('text-2xl font-black', s.color)}>{s.value.toLocaleString()}</p>
          </div>
        ))}
      </div>

      <div className="grid gap-4">
        {loading ? (
          Array.from({ length: 4 }).map((_, i) => <div key={i} className="h-24 skeleton rounded-2xl" />)
        ) : ads.length === 0 ? (
          <div className="text-center py-16 text-muted-foreground bg-card border border-border rounded-2xl">
            <Megaphone className="w-12 h-12 mx-auto mb-3 text-muted-foreground/30" />
            <p className="font-medium">No advertisements yet</p>
            <p className="text-sm mt-1">Create your first campaign with AI-powered ad copy</p>
          </div>
        ) : ads.map(ad => (
          <div key={ad.id} className="bg-card border border-border rounded-2xl p-5 flex flex-col sm:flex-row gap-4">
            {ad.desktop_image_url ? (
              <img src={ad.desktop_image_url} alt="" className="w-full sm:w-32 h-20 object-cover rounded-xl flex-shrink-0" />
            ) : (
              <div className="w-full sm:w-32 h-20 rounded-xl flex-shrink-0 bg-gradient-to-br from-brand-500/20 to-navy-600/20 flex items-center justify-center">
                <ImageIcon className="w-6 h-6 text-brand-500/40" />
              </div>
            )}
            <div className="flex-1 min-w-0">
              <div className="flex items-start justify-between gap-3">
                <div>
                  <h3 className="font-bold">{ad.campaign_name}</h3>
                  {ad.title && <p className="text-sm text-muted-foreground">{ad.title}</p>}
                </div>
                <span className={cn('text-xs font-medium px-2.5 py-1 rounded-full capitalize flex-shrink-0', statusColors[ad.status] || '')}>
                  {ad.status}
                </span>
              </div>
              <div className="flex flex-wrap gap-3 mt-3 text-xs text-muted-foreground">
                <span>Type: <span className="text-foreground font-medium capitalize">{ad.type.replace('_', ' ')}</span></span>
                <span>Priority: <span className="text-foreground font-medium">{ad.priority}</span></span>
                <span>Placements: <span className="text-foreground font-medium">{ad.placement.join(', ')}</span></span>
                <span>Views: <span className="text-foreground font-medium">{ad.view_count}</span></span>
                <span>Clicks: <span className="text-foreground font-medium">{ad.click_count}</span></span>
                {ad.expiry_date && <span>Expires: <span className="text-foreground font-medium">{format(new Date(ad.expiry_date), 'dd MMM yyyy')}</span></span>}
              </div>
            </div>
            <div className="flex items-center gap-2 flex-shrink-0">
              <button onClick={() => toggleStatus(ad)} className="p-2 hover:bg-muted rounded-lg transition-colors" title={ad.status === 'active' ? 'Pause' : 'Activate'}>
                {ad.status === 'active' ? <EyeOff className="w-4 h-4 text-orange-500" /> : <Eye className="w-4 h-4 text-green-500" />}
              </button>
              <button onClick={() => openEdit(ad)} className="p-2 hover:bg-muted rounded-lg transition-colors">
                <Edit className="w-4 h-4 text-muted-foreground" />
              </button>
              <button onClick={() => deleteAd(ad.id)} className="p-2 hover:bg-red-50 rounded-lg transition-colors">
                <Trash2 className="w-4 h-4 text-muted-foreground hover:text-red-600" />
              </button>
            </div>
          </div>
        ))}
      </div>

      {/* Form Modal */}
      {showForm && (
        <div className="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
          <div className="bg-background border border-border rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto shadow-2xl">
            {/* Modal Header */}
            <div className="flex items-center justify-between p-5 border-b border-border sticky top-0 bg-background z-10">
              <h2 className="font-bold text-lg flex items-center gap-2">
                {editing ? <Edit className="w-5 h-5" /> : <Plus className="w-5 h-5" />}
                {editing ? 'Edit Advertisement' : 'New Advertisement'}
              </h2>
              <button onClick={() => setShowForm(false)} className="p-2 hover:bg-muted rounded-lg text-lg">×</button>
            </div>

            <div className="p-5 space-y-5">
              {/* Campaign Name + Type */}
              <div className="grid sm:grid-cols-2 gap-4">
                <div className="sm:col-span-2">
                  <label className="text-xs font-medium mb-1 block">Campaign Name *</label>
                  <input
                    value={formData.campaign_name}
                    onChange={e => setFormData(d => ({ ...d, campaign_name: e.target.value }))}
                    placeholder="e.g. August Intake Early Bird"
                    className="w-full px-3 py-2.5 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500"
                  />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Type</label>
                  <select value={formData.type} onChange={e => setFormData(d => ({ ...d, type: e.target.value }))} className="w-full px-3 py-2.5 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                    {types.map(t => <option key={t} value={t}>{t.replace(/_/g, ' ')}</option>)}
                  </select>
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Status</label>
                  <select value={formData.status} onChange={e => setFormData(d => ({ ...d, status: e.target.value }))} className="w-full px-3 py-2.5 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="draft">Draft</option>
                    <option value="active">Active</option>
                    <option value="paused">Paused</option>
                    <option value="archived">Archived</option>
                  </select>
                </div>
              </div>

              {/* AI Copy Generator */}
              <div className="bg-gradient-to-br from-brand-50 to-blue-50 border border-brand-200 rounded-2xl p-4 space-y-3">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-2">
                    <div className="w-8 h-8 bg-gradient-to-br from-brand-500 to-navy-600 rounded-lg flex items-center justify-center">
                      <Wand2 className="w-4 h-4 text-white" />
                    </div>
                    <div>
                      <p className="font-bold text-sm">AI Ad Copy Generator</p>
                      <p className="text-xs text-muted-foreground">Generate compelling titles & descriptions</p>
                    </div>
                  </div>
                  <Button
                    onClick={handleGenerate}
                    disabled={generating || !formData.campaign_name.trim()}
                    className="bg-gradient-to-r from-brand-500 to-navy-600 hover:from-brand-400 hover:to-blue-500 text-white gap-2"
                    size="sm"
                  >
                    {generating ? <Loader2 className="w-3.5 h-3.5 animate-spin" /> : <Sparkles className="w-3.5 h-3.5" />}
                    {generating ? 'Generating...' : 'Generate Copy'}
                  </Button>
                </div>

                {aiVariations.length > 0 && (
                  <div className="space-y-2">
                    <p className="text-xs font-semibold text-muted-foreground uppercase tracking-wider">3 Variations Generated</p>
                    {aiVariations.map((v, i) => (
                      <div
                        key={i}
                        className={cn(
                          'bg-background border rounded-xl p-3 cursor-pointer transition-all hover:border-brand-500',
                          selectedVariation === i ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-border'
                        )}
                        onClick={() => applyVariation(i)}
                      >
                        <div className="flex items-start justify-between gap-2 mb-1">
                          <div className="flex items-center gap-1.5">
                            <span className="text-xs font-bold bg-brand-100 text-brand-700 px-2 py-0.5 rounded-full">
                              Variation {i + 1}
                            </span>
                            {selectedVariation === i && (
                              <span className="text-xs text-green-600 font-medium flex items-center gap-0.5">
                                <Copy className="w-3 h-3" /> Applied
                              </span>
                            )}
                          </div>
                          <button
                            onClick={(e) => { e.stopPropagation(); handleGenerate(); }}
                            className="text-xs text-muted-foreground hover:text-brand-600 flex items-center gap-0.5"
                            title="Regenerate"
                          >
                            <RefreshCw className="w-3 h-3" />
                          </button>
                        </div>
                        <p className="font-bold text-sm mb-0.5">{v.title}</p>
                        <p className="text-xs text-brand-600 font-medium mb-1">{v.subtitle}</p>
                        <p className="text-xs text-muted-foreground line-clamp-2">{v.description}</p>
                        <p className="text-xs mt-1.5 inline-block bg-brand-50 text-brand-700 px-2 py-0.5 rounded font-medium">
                          CTA: {v.cta_text}
                        </p>
                      </div>
                    ))}
                  </div>
                )}
              </div>

              {/* Ad Content Fields */}
              <div className="grid sm:grid-cols-2 gap-4">
                <div className="sm:col-span-2">
                  <label className="text-xs font-medium mb-1 block">Title</label>
                  <input value={formData.title} onChange={e => setFormData(d => ({ ...d, title: e.target.value }))} placeholder="Ad headline" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Subtitle</label>
                  <input value={formData.subtitle} onChange={e => setFormData(d => ({ ...d, subtitle: e.target.value }))} placeholder="Eye-catching subtitle" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">CTA Text</label>
                  <input value={formData.cta_text} onChange={e => setFormData(d => ({ ...d, cta_text: e.target.value }))} placeholder="e.g. Book Now" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div className="sm:col-span-2">
                  <label className="text-xs font-medium mb-1 block">Description</label>
                  <textarea value={formData.description} onChange={e => setFormData(d => ({ ...d, description: e.target.value }))} placeholder="Ad description / body copy" rows={3} className="w-full px-3 py-2.5 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none" />
                </div>
                <div className="sm:col-span-2">
                  <ImageUpload value={formData.desktop_image_url} onChange={url => setFormData(d => ({ ...d, desktop_image_url: url }))} folder="advertisements" label="Image (optional — used as background)" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">CTA Link</label>
                  <input value={formData.cta_link} onChange={e => setFormData(d => ({ ...d, cta_link: e.target.value }))} placeholder="/booking or /programs/..." className="w-full px-3 py-2.5 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Priority (higher = shown first)</label>
                  <input type="number" value={formData.priority} onChange={e => setFormData(d => ({ ...d, priority: e.target.value }))} className="w-full px-3 py-2.5 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Publish Date</label>
                  <input type="datetime-local" value={formData.publish_date} onChange={e => setFormData(d => ({ ...d, publish_date: e.target.value }))} className="w-full px-3 py-2.5 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Expiry Date</label>
                  <input type="datetime-local" value={formData.expiry_date} onChange={e => setFormData(d => ({ ...d, expiry_date: e.target.value }))} className="w-full px-3 py-2.5 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                </div>
                <div>
                  <label className="text-xs font-medium mb-1 block">Background Color</label>
                  <div className="flex gap-2">
                    <input type="color" value={formData.background_color} onChange={e => setFormData(d => ({ ...d, background_color: e.target.value }))} className="w-10 h-9 border border-border rounded-lg cursor-pointer" />
                    <input value={formData.background_color} onChange={e => setFormData(d => ({ ...d, background_color: e.target.value }))} className="flex-1 px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                  </div>
                </div>
                <div className="sm:col-span-2">
                  <label className="text-xs font-medium mb-1.5 block">Placements</label>
                  <div className="flex flex-wrap gap-2">
                    {placements.map(p => (
                      <label key={p} className="flex items-center gap-1.5 cursor-pointer text-sm">
                        <input
                          type="checkbox"
                          checked={formData.placement.includes(p)}
                          onChange={e => {
                            setFormData(d => ({
                              ...d,
                              placement: e.target.checked
                                ? [...d.placement, p]
                                : d.placement.filter(x => x !== p),
                            }));
                          }}
                          className="w-3.5 h-3.5 rounded"
                        />
                        <span className="capitalize">{p}</span>
                      </label>
                    ))}
                  </div>
                </div>
              </div>

              {/* Live Preview */}
              {formData.title && (
                <div className="space-y-2">
                  <p className="text-xs font-semibold text-muted-foreground uppercase tracking-wider flex items-center gap-1">
                    <Eye className="w-3 h-3" /> Live Preview
                  </p>
                  <div
                    className="relative overflow-hidden rounded-2xl"
                    style={{ backgroundColor: formData.background_color || '#0c4a6e' }}
                  >
                    {formData.desktop_image_url && (
                      <div className="absolute inset-0 bg-cover bg-center opacity-30" style={{ backgroundImage: `url(${formData.desktop_image_url})` }} />
                    )}
                    <div className="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent" />
                    <div className="relative p-6 flex flex-col sm:flex-row items-center gap-4">
                      <div className="flex-1 text-center sm:text-left">
                        {formData.subtitle && <p className="text-xs text-white/60 font-semibold uppercase tracking-wider mb-1">{formData.subtitle}</p>}
                        <h3 className="text-xl font-black text-white mb-1">{formData.title}</h3>
                        {formData.description && <p className="text-sm text-white/70 line-clamp-2">{formData.description}</p>}
                      </div>
                      {formData.cta_text && (
                        <div className="flex-shrink-0 bg-gradient-to-r from-brand-500 to-navy-600 text-white font-bold text-sm px-5 py-2.5 rounded-lg">
                          {formData.cta_text}
                        </div>
                      )}
                    </div>
                  </div>
                </div>
              )}

              <div className="flex justify-end gap-3 pt-2 border-t border-border">
                <Button variant="outline" onClick={() => setShowForm(false)}>Cancel</Button>
                <Button onClick={handleSave} disabled={saving} className="bg-brand-500 hover:bg-brand-600 text-white gap-2">
                  {saving ? <Loader2 className="w-4 h-4 animate-spin" /> : null}
                  {saving ? 'Saving...' : (editing ? 'Update Ad' : 'Create Ad')}
                </Button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
