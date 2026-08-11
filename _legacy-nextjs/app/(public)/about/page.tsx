'use client';
import { useEffect, useState } from 'react';
import { supabase } from '@/lib/supabase';
import { Target, Eye, Heart, ArrowRight, Linkedin, Twitter } from 'lucide-react';
import type { TeamMember, Trainer, Testimonial, Statistic } from '@/lib/database.types';
import Link from 'next/link';

export default function AboutPage() {
  const [team, setTeam] = useState<TeamMember[]>([]);
  const [trainers, setTrainers] = useState<Trainer[]>([]);
  const [testimonials, setTestimonials] = useState<Testimonial[]>([]);
  const [stats, setStats] = useState<Statistic[]>([]);

  useEffect(() => {
    Promise.all([
      supabase.from('team_members').select('*').eq('is_active', true).order('order_index'),
      supabase.from('trainers').select('*').eq('is_active', true).order('order_index'),
      supabase.from('testimonials').select('*').eq('is_featured', true).eq('is_published', true).limit(3),
      supabase.from('statistics').select('*').eq('is_active', true),
    ]).then(([teamRes, trainersRes, testimonialsRes, statsRes]) => {
      if (teamRes.data) setTeam(teamRes.data);
      if (trainersRes.data) setTrainers(trainersRes.data);
      if (testimonialsRes.data) setTestimonials(testimonialsRes.data);
      if (statsRes.data) setStats(statsRes.data);
    });
  }, []);

  const statsByKey = Object.fromEntries(stats.filter(s => s.key).map(s => [s.key as string, s]));
  const studentsTrained = statsByKey.students_trained?.value || '2,000+';
  const successRate = statsByKey.success_rate?.value || '94%';

  const timeline = [
    { year: '2019', title: 'Founded', desc: 'Kazilink Digital Academy launched with our first freelancing cohort of 12 students in a co-working space in Nyandarua.' },
    { year: '2020', title: 'First 50 Graduates', desc: 'Despite the pandemic, we pivoted fully online and trained over 50 students across Nyandarua.' },
    { year: '2021', title: 'Expanded Programs', desc: 'Added E-Commerce, Digital Marketing, and Web Development to our course catalog. Grew to 5 trainers.' },
    { year: '2022', title: '500 Students', desc: 'Hit 500 enrolled students. Partnered with other stakeholders for Startups programs.' },
    { year: '2023', title: 'Regional Expansion', desc: 'Expanded to other cuties and launched Kazilink Online Income Initiative.' },
    { year: '2024', title: '1000 Students', desc: 'Celebrated 1000 graduates. Launched corporate training packages for Kenyan SMEs.' },
    { year: '2026', title: 'Today', desc: `${studentsTrained} students trained, ${successRate} employment/income rate. Building the next generation of African digital entrepreneurs.` },
  ];

  return (
    <>
      {/* Hero */}
      <div className="relative bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-20 sm:py-28 overflow-hidden">
        <div className="absolute inset-0 bg-[url(https://images.pexels.com/photos/3184338/pexels-photo-3184338.jpeg?auto=compress&cs=tinysrgb&w=1600)] bg-cover bg-center opacity-10" />
        <div className="relative max-w-4xl mx-auto px-4 sm:px-6 text-center">
          <span className="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-4 block">About Us</span>
          <h1 className="text-4xl sm:text-5xl lg:text-6xl font-black text-white mb-6">
            We Train Kenyans to<br />Earn Online
          </h1>
          <p className="text-lg text-gray-300 leading-relaxed max-w-2xl mx-auto">
            Kazilink Digital Academy was born from a simple belief: every African with internet access and the right skills can earn a world-class income from anywhere.
          </p>
        </div>
      </div>

      {/* Mission, Vision, Values */}
      <section className="section-padding bg-background">
        <div className="max-w-7xl mx-auto container-padding">
          <div className="grid md:grid-cols-3 gap-8">
            {[
              {
                icon: <Target className="w-8 h-8 text-brand-500" />,
                title: 'Our Mission',
                text: 'To equip 1 million Africans with practical digital skills that enable them to earn sustainable income online, reducing unemployment and poverty across the continent.',
                color: 'border-brand-500/20 bg-brand-50',
              },
              {
                icon: <Eye className="w-8 h-8 text-orange-500" />,
                title: 'Our Vision',
                text: "Africa's most impactful digital skills academy — where every graduate earns more, achieves more, and inspires others to do the same.",
                color: 'border-orange-500/20 bg-orange-50',
              },
              {
                icon: <Heart className="w-8 h-8 text-green-500" />,
                title: 'Our Values',
                text: 'Practical over theoretical. Results over certificates. Community over competition. Integrity in everything we do.',
                color: 'border-green-500/20 bg-green-50',
              },
            ].map(item => (
              <div key={item.title} className={`border-2 rounded-2xl p-7 ${item.color}`}>
                <div className="mb-4">{item.icon}</div>
                <h3 className="text-xl font-bold mb-3">{item.title}</h3>
                <p className="text-muted-foreground leading-relaxed text-sm">{item.text}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Story */}
      <section className="section-padding bg-muted/30">
        <div className="max-w-4xl mx-auto container-padding">
          <div className="text-center mb-10">
            <span className="text-brand-600 font-semibold text-sm uppercase tracking-wider mb-2 block">Our Story</span>
            <h2 className="text-3xl sm:text-4xl font-black">How Kazilink Began</h2>
          </div>
          <div className="prose prose-lg max-w-none text-muted-foreground space-y-4">
            <p>In 2019, XYZ was working as a software engineer in Nyandarua when hee noticed something troubling: Kenya had millions of educated, ambitious young people — but most were either unemployed or trapped in low-paying jobs that didn't match their potential.</p>
            <p>Meanwhile, he was watching friends in her network quietly earning more than Ksh 100K per month from their laptops — freelancing, selling online, running digital agencies. The skills required weren't inaccessible, they just weren't being taught.</p>
            <p>He quit her corporate job, brought together a team of top digital practitioners, and launched Kazilink Digital Academy with one mission: teach the real skills that pay, to anyone willing to learn.</p>
            <p>Seven years later, Kazilink has trained over {studentsTrained} students across Kenya. Our graduates run businesses, lead digital teams, and work for clients worldwide — all from the comfort of their homes.</p>
          </div>
        </div>
      </section>

      {/* Timeline */}
      <section className="section-padding bg-background">
        <div className="max-w-4xl mx-auto container-padding">
          <div className="text-center mb-12">
            <span className="text-brand-600 font-semibold text-sm uppercase tracking-wider mb-2 block">Journey</span>
            <h2 className="text-3xl sm:text-4xl font-black">Our Timeline</h2>
          </div>
          <div className="relative">
            <div className="absolute left-4 md:left-1/2 top-0 bottom-0 w-0.5 bg-border" />
            <div className="space-y-10">
              {timeline.map((item, i) => (
                <div key={item.year} className={`relative flex gap-8 ${i % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse'}`}>
                  <div className={`flex-1 ${i % 2 === 0 ? 'md:text-right' : 'md:text-left'} pl-10 md:pl-0`}>
                    <div className="bg-card border border-border rounded-2xl p-5 inline-block w-full md:max-w-xs">
                      <span className="text-brand-600 font-black text-lg">{item.year}</span>
                      <h3 className="font-bold mt-0.5">{item.title}</h3>
                      <p className="text-sm text-muted-foreground mt-1">{item.desc}</p>
                    </div>
                  </div>
                  <div className="absolute left-0 md:left-1/2 md:-translate-x-1/2 top-5 w-8 h-8 bg-brand-500 rounded-full flex items-center justify-center shadow-lg shadow-brand-500/30 flex-shrink-0">
                    <div className="w-3 h-3 bg-white rounded-full" />
                  </div>
                  <div className="flex-1 hidden md:block" />
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Team */}
      {team.length > 0 && (
        <section className="section-padding bg-muted/30">
          <div className="max-w-7xl mx-auto container-padding">
            <div className="text-center mb-10">
              <span className="text-brand-600 font-semibold text-sm uppercase tracking-wider mb-2 block">Leadership</span>
              <h2 className="text-3xl sm:text-4xl font-black">Meet the Team</h2>
              <p className="text-muted-foreground mt-2">The people behind Kazilink Digital Academy's mission.</p>
            </div>
            <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {team.map(member => (
                <div key={member.id} className="bg-card border border-border rounded-2xl p-5 text-center card-hover">
                  {member.avatar_url ? (
                    <img src={member.avatar_url} alt={member.full_name} className="w-20 h-20 rounded-full object-cover mx-auto mb-4" />
                  ) : (
                    <div className="w-20 h-20 rounded-full bg-brand-500 text-white font-bold text-2xl flex items-center justify-center mx-auto mb-4">
                      {member.full_name[0]}
                    </div>
                  )}
                  <h3 className="font-bold">{member.full_name}</h3>
                  <p className="text-xs text-brand-600 mt-0.5 mb-2">{member.title}</p>
                  {member.bio && <p className="text-xs text-muted-foreground line-clamp-3">{member.bio}</p>}
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Trainers */}
      {trainers.length > 0 && (
        <section id="trainers" className="section-padding bg-background">
          <div className="max-w-7xl mx-auto container-padding">
            <div className="text-center mb-10">
              <span className="text-brand-600 font-semibold text-sm uppercase tracking-wider mb-2 block">Expert Trainers</span>
              <h2 className="text-3xl sm:text-4xl font-black">Learn From Practitioners</h2>
            </div>
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {trainers.map(trainer => (
                <div key={trainer.id} className="bg-card border border-border rounded-2xl p-6 card-hover">
                  <div className="flex items-start gap-4 mb-4">
                    {trainer.avatar_url ? (
                      <img src={trainer.avatar_url} alt={trainer.full_name} className="w-16 h-16 rounded-xl object-cover flex-shrink-0" />
                    ) : (
                      <div className="w-16 h-16 rounded-xl bg-brand-500 text-white font-bold text-xl flex items-center justify-center flex-shrink-0">
                        {trainer.full_name[0]}
                      </div>
                    )}
                    <div>
                      <h3 className="font-bold">{trainer.full_name}</h3>
                      {trainer.title && <p className="text-xs text-brand-600">{trainer.title}</p>}
                    </div>
                  </div>
                  {trainer.bio && <p className="text-sm text-muted-foreground mb-4 line-clamp-3">{trainer.bio}</p>}
                  {trainer.specializations && (
                    <div className="flex flex-wrap gap-1.5">
                      {trainer.specializations.slice(0, 4).map(s => (
                        <span key={s} className="text-xs bg-brand-50 text-brand-700 px-2 py-0.5 rounded-full">{s}</span>
                      ))}
                    </div>
                  )}
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* CTA */}
      <section className="py-20 bg-gradient-to-br from-brand-600 to-navy-700">
        <div className="max-w-3xl mx-auto container-padding text-center">
          <h2 className="text-3xl sm:text-4xl font-black text-white mb-4">Ready to Change Your Life?</h2>
          <p className="text-brand-100 mb-8">Join {studentsTrained} graduates who chose to invest in themselves. The next cohort starts soon.</p>
          <div className="flex flex-col sm:flex-row gap-3 justify-center">
            <Link href="/booking" className="bg-white text-brand-700 hover:bg-brand-50 font-bold px-7 py-4 rounded-xl transition-colors inline-flex items-center gap-2 justify-center">
              Enroll Now <ArrowRight className="w-4 h-4" />
            </Link>
            <Link href="/contact" className="border-2 border-white/40 text-white hover:border-white font-semibold px-7 py-4 rounded-xl transition-colors inline-flex items-center justify-center">
              Contact Us
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}
