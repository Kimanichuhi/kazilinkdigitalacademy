'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { toast } from 'sonner';
import { Mail, Phone, MapPin, MessageCircle, Loader2, CheckCircle } from 'lucide-react';
import { Button } from '@/components/ui/button';
import type { SiteSettingKV } from '@/lib/database.types';

const contactSchema = z.object({
  full_name: z.string().min(2, 'Name required'),
  email: z.string().email('Valid email required'),
  phone: z.string().optional(),
  subject: z.string().optional(),
  message: z.string().min(10, 'Message must be at least 10 characters'),
});

type ContactFormData = z.infer<typeof contactSchema>;

export default function ContactPage() {
  const [settings, setSettings] = useState<Record<string, string>>({});
  const [submitting, setSubmitting] = useState(false);
  const [submitted, setSubmitted] = useState(false);

  const { register, handleSubmit, formState: { errors }, reset } = useForm<ContactFormData>({
    resolver: zodResolver(contactSchema),
  });

  useEffect(() => {
    supabase.from('site_settings').select('key,value').in('key', [
      'contact_email', 'contact_phone', 'contact_address', 'contact_whatsapp',
      'social_facebook', 'social_instagram', 'social_linkedin',
    ]).then(({ data }) => {
      if (data) {
        const map: Record<string, string> = {};
        data.forEach((s: SiteSettingKV) => { if (s.value) map[s.key] = s.value; });
        setSettings(map);
      }
    });
  }, []);

  async function onSubmit(data: ContactFormData) {
    setSubmitting(true);
    const { error } = await supabase.from('contact_submissions').insert({
      full_name: data.full_name,
      email: data.email,
      phone: data.phone || null,
      subject: data.subject || null,
      message: data.message,
    });

    if (error) {
      toast.error('Failed to send message. Please try again.');
    } else {
      setSubmitted(true);
      reset();
    }
    setSubmitting(false);
  }

  return (
    <>
      <div className="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 text-center">
          <span className="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Get In Touch</span>
          <h1 className="text-4xl sm:text-5xl font-black text-white mb-4">Contact Us</h1>
          <p className="text-gray-400 text-lg">Have a question? We'd love to hear from you.</p>
        </div>
      </div>

      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid lg:grid-cols-5 gap-10">
          {/* Contact Info */}
          <div className="lg:col-span-2 space-y-6">
            <div>
              <h2 className="text-2xl font-bold mb-6">Get In Touch</h2>
              <div className="space-y-4">
                {settings.contact_email && (
                  <a href={`mailto:${settings.contact_email}`} className="flex items-start gap-4 p-4 bg-card border border-border rounded-xl hover:border-brand-500 transition-colors group">
                    <div className="w-10 h-10 bg-brand-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-brand-500 transition-colors">
                      <Mail className="w-5 h-5 text-brand-600 group-hover:text-white transition-colors" />
                    </div>
                    <div>
                      <p className="text-xs text-muted-foreground mb-0.5">Email</p>
                      <p className="font-medium text-sm">{settings.contact_email}</p>
                    </div>
                  </a>
                )}
                {settings.contact_phone && (
                  <a href={`tel:${settings.contact_phone}`} className="flex items-start gap-4 p-4 bg-card border border-border rounded-xl hover:border-brand-500 transition-colors group">
                    <div className="w-10 h-10 bg-brand-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-brand-500 transition-colors">
                      <Phone className="w-5 h-5 text-brand-600 group-hover:text-white transition-colors" />
                    </div>
                    <div>
                      <p className="text-xs text-muted-foreground mb-0.5">Phone / WhatsApp</p>
                      <p className="font-medium text-sm">{settings.contact_phone}</p>
                    </div>
                  </a>
                )}
                {settings.contact_whatsapp && (
                  <a
                    href={`https://wa.me/${settings.contact_whatsapp.replace(/\D/g, '')}`}
                    target="_blank" rel="noopener noreferrer"
                    className="flex items-start gap-4 p-4 bg-green-50 border border-green-200 rounded-xl hover:border-green-500 transition-colors group"
                  >
                    <div className="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                      <MessageCircle className="w-5 h-5 text-green-600" />
                    </div>
                    <div>
                      <p className="text-xs text-muted-foreground mb-0.5">WhatsApp</p>
                      <p className="font-medium text-sm text-green-700">Chat with us on WhatsApp</p>
                    </div>
                  </a>
                )}
                {settings.contact_address && (
                  <div className="flex items-start gap-4 p-4 bg-card border border-border rounded-xl">
                    <div className="w-10 h-10 bg-brand-100 rounded-xl flex items-center justify-center flex-shrink-0">
                      <MapPin className="w-5 h-5 text-brand-600" />
                    </div>
                    <div>
                      <p className="text-xs text-muted-foreground mb-0.5">Office</p>
                      <p className="font-medium text-sm">{settings.contact_address}</p>
                    </div>
                  </div>
                )}
              </div>
            </div>

            <div className="bg-gradient-to-br from-brand-500 to-navy-600 rounded-2xl p-6 text-white">
              <h3 className="font-bold text-lg mb-2">Office Hours</h3>
              <div className="space-y-1.5 text-sm text-brand-100">
                <p>Monday – Friday: 8:00 AM – 6:00 PM</p>
                <p>Saturday: 9:00 AM – 2:00 PM</p>
                <p>Sunday: Closed</p>
              </div>
              <p className="text-xs text-brand-200 mt-3">All times are EAT (East Africa Time)</p>
            </div>
          </div>

          {/* Contact Form */}
          <div className="lg:col-span-3">
            <div className="bg-card border border-border rounded-2xl p-6 sm:p-8">
              {submitted ? (
                <div className="text-center py-12">
                  <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <CheckCircle className="w-8 h-8 text-green-600" />
                  </div>
                  <h3 className="text-xl font-bold mb-2">Message Sent!</h3>
                  <p className="text-muted-foreground">Thank you for reaching out. Our team will respond within 24 hours.</p>
                  <Button variant="outline" className="mt-6" onClick={() => setSubmitted(false)}>Send Another Message</Button>
                </div>
              ) : (
                <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
                  <h2 className="text-xl font-bold mb-6">Send Us a Message</h2>
                  <div className="grid sm:grid-cols-2 gap-4">
                    <div>
                      <label className="text-sm font-medium mb-1.5 block">Full Name *</label>
                      <input {...register('full_name')} placeholder="Your name" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
                      {errors.full_name && <p className="text-red-500 text-xs mt-1">{errors.full_name.message}</p>}
                    </div>
                    <div>
                      <label className="text-sm font-medium mb-1.5 block">Email Address *</label>
                      <input {...register('email')} type="email" placeholder="you@email.com" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
                      {errors.email && <p className="text-red-500 text-xs mt-1">{errors.email.message}</p>}
                    </div>
                  </div>
                  <div className="grid sm:grid-cols-2 gap-4">
                    <div>
                      <label className="text-sm font-medium mb-1.5 block">Phone Number</label>
                      <input {...register('phone')} placeholder="+254 700 000 000" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background" />
                    </div>
                    <div>
                      <label className="text-sm font-medium mb-1.5 block">Subject</label>
                      <select {...register('subject')} className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background">
                        <option value="">Select a topic</option>
                        <option value="enrollment">Program Enrollment</option>
                        <option value="payment">Payment & Fees</option>
                        <option value="technical">Technical Support</option>
                        <option value="corporate">Corporate Training</option>
                        <option value="other">Other</option>
                      </select>
                    </div>
                  </div>
                  <div>
                    <label className="text-sm font-medium mb-1.5 block">Message *</label>
                    <textarea {...register('message')} rows={5} placeholder="How can we help you?" className="w-full px-3 py-2.5 text-sm border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 bg-background resize-none" />
                    {errors.message && <p className="text-red-500 text-xs mt-1">{errors.message.message}</p>}
                  </div>
                  <Button type="submit" disabled={submitting} className="w-full bg-brand-500 hover:bg-brand-600 text-white gap-2">
                    {submitting ? <><Loader2 className="w-4 h-4 animate-spin" /> Sending...</> : 'Send Message'}
                  </Button>
                </form>
              )}
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
