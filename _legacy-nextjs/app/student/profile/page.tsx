'use client';
import { useState } from 'react';
import { supabase } from '@/lib/supabase';
import { useAuth } from '@/components/providers/AuthProvider';
import { useForm } from 'react-hook-form';
import { toast } from 'sonner';
import { Loader2, Save } from 'lucide-react';
import { Button } from '@/components/ui/button';

export default function StudentProfilePage() {
  const { user, profile } = useAuth();
  const [saving, setSaving] = useState(false);

  const { register, handleSubmit, formState: { errors } } = useForm({
    defaultValues: {
      full_name: profile?.full_name || '',
      phone: profile?.phone || '',
      bio: profile?.bio || '',
    },
  });

  async function onSubmit(data: { full_name: string; phone: string; bio: string }) {
    if (!user) return;
    setSaving(true);
    const { error } = await supabase.from('profiles').update({
      full_name: data.full_name,
      phone: data.phone,
      bio: data.bio,
      updated_at: new Date().toISOString(),
    }).eq('id', user.id);

    if (error) { toast.error('Failed to update profile'); } else { toast.success('Profile updated!'); }
    setSaving(false);
  }

  return (
    <div className="max-w-2xl mx-auto px-4 sm:px-6 py-10">
      <h1 className="text-3xl font-black mb-8">Edit Profile</h1>
      <div className="bg-card border border-border rounded-2xl p-6">
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-5">
          <div>
            <label className="text-sm font-medium mb-1.5 block">Full Name</label>
            <input {...register('full_name')} className="w-full px-3 py-2.5 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
          </div>
          <div>
            <label className="text-sm font-medium mb-1.5 block">Email</label>
            <input value={user?.email || ''} disabled className="w-full px-3 py-2.5 border border-border rounded-xl text-sm bg-muted text-muted-foreground cursor-not-allowed" />
            <p className="text-xs text-muted-foreground mt-1">Email cannot be changed here</p>
          </div>
          <div>
            <label className="text-sm font-medium mb-1.5 block">Phone Number</label>
            <input {...register('phone')} className="w-full px-3 py-2.5 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
          </div>
          <div>
            <label className="text-sm font-medium mb-1.5 block">Bio</label>
            <textarea {...register('bio')} rows={3} placeholder="Tell us a bit about yourself..." className="w-full px-3 py-2.5 border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background resize-none" />
          </div>
          <Button type="submit" disabled={saving} className="bg-brand-500 hover:bg-brand-600 text-white gap-2">
            {saving ? <><Loader2 className="w-4 h-4 animate-spin" /> Saving...</> : <><Save className="w-4 h-4" /> Save Profile</>}
          </Button>
        </form>
      </div>
    </div>
  );
}
