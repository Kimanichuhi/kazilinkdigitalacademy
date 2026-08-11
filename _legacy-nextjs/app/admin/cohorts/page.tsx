'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { toast } from 'sonner';
import { Plus, Edit, Trash2, AlertTriangle } from 'lucide-react';
import type { Cohort, Program, Trainer } from '@/lib/database.types';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { format } from 'date-fns';

const statusColors: Record<string, string> = {
  upcoming: 'bg-blue-100 text-blue-700',
  open: 'bg-green-100 text-green-700',
  full: 'bg-red-100 text-red-700',
  in_progress: 'bg-orange-100 text-orange-700',
  completed: 'bg-gray-100 text-gray-600',
  cancelled: 'bg-gray-100 text-gray-500',
};

export default function AdminCohortsPage() {
  const [cohorts, setCohorts] = useState<(Cohort & { programs: Program; trainers: Trainer | null })[]>([]);
  const [programs, setPrograms] = useState<Program[]>([]);
  const [trainers, setTrainers] = useState<Trainer[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editing, setEditing] = useState<Cohort | null>(null);
  const [saving, setSaving] = useState(false);
  const [formData, setFormData] = useState({
    program_id: '', trainer_id: '', name: '', start_date: '', end_date: '',
    registration_deadline: '', total_seats: '20', schedule_details: '',
    venue: '', online_link: '', online_platform: '', price: '', currency: 'KES', status: 'upcoming',
  });

  useEffect(() => {
    load();
    // Fetch every program/trainer (not just available ones) so a cohort's
    // existing assignment still shows up correctly if it was later
    // unpublished/deactivated — see programOptions/trainerOptions below.
    supabase.from('programs').select('id,title,is_published,is_active').order('title').then(({ data }) => { if (data) setPrograms(data as Program[]); });
    supabase.from('trainers').select('id,full_name,is_active').order('full_name').then(({ data }) => { if (data) setTrainers(data as Trainer[]); });
  }, []);

  const availablePrograms = programs.filter(p => p.is_published && p.is_active);
  const activeTrainers = trainers.filter(t => t.is_active);
  const noAvailablePrograms = programs.length > 0 && availablePrograms.length === 0;

  const programOptions = programs.map(p => {
    const available = p.is_published && p.is_active;
    return { value: p.id, label: available ? p.title : `${p.title} (unavailable)`, disabled: !available && p.id !== formData.program_id };
  });
  const trainerOptions = trainers.map(t => ({
    value: t.id,
    label: t.is_active ? t.full_name : `${t.full_name} (inactive)`,
    disabled: !t.is_active && t.id !== formData.trainer_id,
  }));

  async function load() {
    const { data } = await supabase.from('cohorts').select('*, programs(*), trainers(*)').order('start_date', { ascending: false });
    if (data) setCohorts(data as (Cohort & { programs: Program; trainers: Trainer | null })[]);
    setLoading(false);
  }

  async function handleSave() {
    if (!formData.program_id || !formData.name || !formData.start_date) { toast.error('Program, name, and start date required'); return; }
    setSaving(true);
    const payload = {
      ...formData,
      total_seats: parseInt(formData.total_seats) || 20,
      price: formData.price ? parseFloat(formData.price) : null,
      trainer_id: formData.trainer_id || null,
      end_date: formData.end_date || null,
      registration_deadline: formData.registration_deadline || null,
    };
    const op = editing ? supabase.from('cohorts').update({ ...payload, updated_at: new Date().toISOString() }).eq('id', editing.id) : supabase.from('cohorts').insert(payload as any);
    const { error } = await op;
    if (error) { toast.error(error.message); } else { toast.success(editing ? 'Updated' : 'Created'); load(); setShowForm(false); }
    setSaving(false);
  }

  function openEdit(c: Cohort) {
    setEditing(c);
    setFormData({
      program_id: c.program_id, trainer_id: c.trainer_id || '', name: c.name,
      start_date: c.start_date, end_date: c.end_date || '', registration_deadline: c.registration_deadline || '',
      total_seats: c.total_seats.toString(), schedule_details: c.schedule_details || '',
      venue: c.venue || '', online_link: c.online_link || '', online_platform: c.online_platform || '',
      price: c.price?.toString() || '', currency: c.currency, status: c.status,
    });
    setShowForm(true);
  }

  return (
    <div className="p-6 space-y-6">
      <div className="flex items-center justify-between">
        <div><h1 className="text-2xl font-black">Cohorts</h1><p className="text-sm text-muted-foreground">{cohorts.length} cohorts</p></div>
        <Button
          onClick={() => { setEditing(null); setFormData({ program_id: '', trainer_id: '', name: '', start_date: '', end_date: '', registration_deadline: '', total_seats: '20', schedule_details: '', venue: '', online_link: '', online_platform: '', price: '', currency: 'KES', status: 'upcoming' }); setShowForm(true); }}
          disabled={noAvailablePrograms}
          title={noAvailablePrograms ? 'Publish at least one active program before creating a cohort' : undefined}
          className="bg-brand-500 hover:bg-brand-600 text-white gap-2"
        >
          <Plus className="w-4 h-4" /> New Cohort
        </Button>
      </div>

      {noAvailablePrograms && (
        <div className="flex items-start gap-2.5 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-3.5 text-sm">
          <AlertTriangle className="w-4 h-4 flex-shrink-0 mt-0.5" />
          <span>No published, active programs available. Cohorts need a program to attach to — publish one on the <span className="font-medium">Programs</span> page first.</span>
        </div>
      )}

      <div className="bg-card border border-border rounded-2xl overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-border bg-muted/30">
              <th className="text-left px-5 py-3 font-medium text-muted-foreground">Cohort</th>
              <th className="text-left px-5 py-3 font-medium text-muted-foreground hidden md:table-cell">Program</th>
              <th className="text-left px-5 py-3 font-medium text-muted-foreground">Dates</th>
              <th className="text-left px-5 py-3 font-medium text-muted-foreground">Seats</th>
              <th className="text-left px-5 py-3 font-medium text-muted-foreground">Status</th>
              <th className="text-left px-5 py-3 font-medium text-muted-foreground">Actions</th>
            </tr>
          </thead>
          <tbody>
            {loading ? Array.from({ length: 5 }).map((_, i) => <tr key={i} className="border-b"><td colSpan={6} className="px-5 py-3"><div className="h-4 skeleton rounded" /></td></tr>) :
              cohorts.map(c => (
                <tr key={c.id} className="border-b border-border hover:bg-muted/20 transition-colors">
                  <td className="px-5 py-3"><p className="font-medium">{c.name}</p>{c.schedule_details && <p className="text-xs text-muted-foreground">{c.schedule_details}</p>}</td>
                  <td className="px-5 py-3 hidden md:table-cell text-xs text-muted-foreground">{c.programs?.title}</td>
                  <td className="px-5 py-3 text-xs text-muted-foreground">{format(new Date(c.start_date), 'dd MMM yyyy')}</td>
                  <td className="px-5 py-3"><span className="text-sm">{c.booked_seats}/{c.total_seats}</span></td>
                  <td className="px-5 py-3"><span className={cn('text-xs font-medium px-2 py-0.5 rounded-full capitalize', statusColors[c.status] || '')}>{c.status}</span></td>
                  <td className="px-5 py-3">
                    <div className="flex gap-1.5">
                      <button onClick={() => openEdit(c)} className="p-1.5 hover:bg-muted rounded-lg"><Edit className="w-3.5 h-3.5 text-muted-foreground" /></button>
                      <button onClick={async () => { if (!confirm('Delete cohort?')) return; await supabase.from('cohorts').delete().eq('id', c.id); toast.success('Deleted'); load(); }} className="p-1.5 hover:bg-red-50 rounded-lg"><Trash2 className="w-3.5 h-3.5 text-muted-foreground" /></button>
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
              <h2 className="font-bold">{editing ? 'Edit' : 'New'} Cohort</h2>
              <button onClick={() => setShowForm(false)} className="p-2 hover:bg-muted rounded-lg">×</button>
            </div>
            <div className="p-5 space-y-4">
              <div className="grid sm:grid-cols-2 gap-4">
                {[
                  { key: 'program_id', label: 'Program *', type: 'select', options: programOptions },
                  { key: 'trainer_id', label: 'Trainer', type: 'select', options: trainerOptions },
                  { key: 'name', label: 'Cohort Name *', type: 'text' },
                  { key: 'status', label: 'Status', type: 'select', options: ['upcoming','open','full','in_progress','completed','cancelled'].map(s => ({ value: s, label: s })) },
                  { key: 'start_date', label: 'Start Date *', type: 'date' },
                  { key: 'end_date', label: 'End Date', type: 'date' },
                  { key: 'registration_deadline', label: 'Registration Deadline', type: 'date' },
                  { key: 'total_seats', label: 'Total Seats', type: 'number' },
                  { key: 'price', label: 'Price (override)', type: 'number' },
                  { key: 'venue', label: 'Venue', type: 'text' },
                  { key: 'online_link', label: 'Online Meeting Link', type: 'url' },
                  { key: 'online_platform', label: 'Online Platform', type: 'text' },
                ].map(f => (
                  <div key={f.key}>
                    <label className="text-xs font-medium mb-1 block">{f.label}</label>
                    {f.type === 'select' ? (
                      <select value={(formData as any)[f.key]} onChange={e => setFormData(d => ({ ...d, [f.key]: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">Select...</option>
                        {f.options?.map((o: any) => <option key={o.value} value={o.value} disabled={o.disabled}>{o.label}</option>)}
                      </select>
                    ) : (
                      <input type={f.type} value={(formData as any)[f.key]} onChange={e => setFormData(d => ({ ...d, [f.key]: e.target.value }))} className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
                    )}
                    {f.key === 'trainer_id' && activeTrainers.length === 0 && (
                      <p className="text-xs text-amber-600 mt-1">No active trainers yet — you can leave this unassigned or add one in Trainers.</p>
                    )}
                  </div>
                ))}
                <div className="sm:col-span-2">
                  <label className="text-xs font-medium mb-1 block">Schedule Details</label>
                  <input value={formData.schedule_details} onChange={e => setFormData(d => ({ ...d, schedule_details: e.target.value }))} placeholder="Mon, Wed, Fri | 7:00 PM – 9:00 PM EAT" className="w-full px-3 py-2 text-sm border border-border rounded-xl bg-background focus:outline-none focus:ring-2 focus:ring-brand-500" />
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
