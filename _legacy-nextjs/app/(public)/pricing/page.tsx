'use client';
import Link from 'next/link';
import { Check, ArrowRight, Star } from 'lucide-react';

export default function PricingPage() {
  const plans = [
    {
      name: 'Starter',
      tag: null,
      price: 'KSh 12,000',
      desc: 'Perfect for beginners starting their online income journey.',
      features: [
        'Access to 1 core program',
        'Online live sessions',
        'Study materials',
        'Certificate of completion',
        'Alumni community access',
        '30-day email support',
      ],
      cta: 'Enroll Now',
      href: '/programs',
      highlighted: false,
    },
    {
      name: 'Professional',
      tag: 'Most Popular',
      price: 'KSh 22,000',
      desc: 'For serious learners ready to build a sustainable income.',
      features: [
        'Access to 1 premium program',
        'Live + recorded sessions',
        'Premium study pack',
        'Certificate of completion',
        '1-on-1 coaching session',
        'Job placement support',
        'Alumni community access',
        '90-day mentorship',
      ],
      cta: 'Enroll Now',
      href: '/programs',
      highlighted: true,
    },
    {
      name: 'Enterprise',
      tag: 'Corporate',
      price: 'Custom',
      desc: 'For businesses and organisations training 5+ staff.',
      features: [
        'Multiple programs bundled',
        'Custom training schedule',
        'Dedicated account manager',
        'Progress reporting dashboard',
        'Branded certificates',
        'On-site or virtual delivery',
        'Priority support',
        'Volume discounts',
      ],
      cta: 'Get a Quote',
      href: '/contact',
      highlighted: false,
    },
  ];

  const addOns = [
    { name: 'Extra 1-on-1 Coaching Session', price: 'KSh 2,500' },
    { name: 'Resume & Profile Review', price: 'KSh 1,500' },
    { name: 'Portfolio Review & Feedback', price: 'KSh 2,000' },
    { name: 'Mock Client Interview Prep', price: 'KSh 3,000' },
  ];

  return (
    <>
      <div className="bg-gradient-to-br from-gray-950 via-navy-950 to-gray-900 py-16 sm:py-20">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 text-center">
          <span className="text-brand-400 font-semibold text-sm uppercase tracking-wider mb-3 block">Pricing</span>
          <h1 className="text-4xl sm:text-5xl font-black text-white mb-4">Invest in Yourself</h1>
          <p className="text-gray-400 text-lg">Transparent pricing. No hidden fees. Real ROI guaranteed.</p>
        </div>
      </div>

      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {/* Plans */}
        <div className="grid md:grid-cols-3 gap-6 mb-16">
          {plans.map(plan => (
            <div
              key={plan.name}
              className={`rounded-2xl border-2 p-7 flex flex-col relative overflow-hidden ${
                plan.highlighted
                  ? 'border-brand-500 bg-gradient-to-b from-brand-50 to-white shadow-xl shadow-brand-500/20'
                  : 'border-border bg-card'
              }`}
            >
              {plan.tag && (
                <div className={`absolute top-4 right-4 text-xs font-bold px-2.5 py-1 rounded-full ${plan.highlighted ? 'bg-brand-500 text-white' : 'bg-orange-500 text-white'}`}>
                  {plan.tag}
                </div>
              )}
              <h3 className="text-xl font-black mb-1">{plan.name}</h3>
              <p className="text-muted-foreground text-sm mb-5">{plan.desc}</p>
              <div className="mb-6">
                <span className="text-4xl font-black">{plan.price}</span>
                {plan.price !== 'Custom' && <span className="text-muted-foreground text-sm ml-1">/program</span>}
              </div>
              <ul className="space-y-3 flex-1 mb-7">
                {plan.features.map(f => (
                  <li key={f} className="flex items-start gap-2.5 text-sm">
                    <Check className={`w-4 h-4 flex-shrink-0 mt-0.5 ${plan.highlighted ? 'text-brand-500' : 'text-green-500'}`} />
                    {f}
                  </li>
                ))}
              </ul>
              <Link
                href={plan.href}
                className={`flex items-center justify-center gap-2 font-bold py-3.5 rounded-xl transition-colors ${
                  plan.highlighted
                    ? 'bg-brand-500 hover:bg-brand-600 text-white shadow-lg shadow-brand-500/25'
                    : 'border-2 border-border hover:border-brand-500 hover:text-brand-600 transition-all'
                }`}
              >
                {plan.cta} <ArrowRight className="w-4 h-4" />
              </Link>
            </div>
          ))}
        </div>

        {/* Installments */}
        <div className="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl p-8 mb-10">
          <h2 className="text-2xl font-bold mb-2">Flexible Payment Plans</h2>
          <p className="text-muted-foreground mb-6">Can't pay in full? We offer installment plans to make training accessible to everyone.</p>
          <div className="grid sm:grid-cols-3 gap-5">
            {[
              { name: 'Pay in Full', desc: 'Best value — save 5%', badge: 'Save 5%' },
              { name: '2 Installments', desc: '50% deposit + 50% at Week 3', badge: null },
              { name: '3 Installments', desc: '40% deposit + 30% + 30%', badge: null },
            ].map(p => (
              <div key={p.name} className="bg-white rounded-xl p-5 border border-green-200">
                <div className="flex items-center gap-2 mb-1">
                  <h4 className="font-bold">{p.name}</h4>
                  {p.badge && <span className="text-xs bg-green-500 text-white px-2 py-0.5 rounded-full">{p.badge}</span>}
                </div>
                <p className="text-sm text-muted-foreground">{p.desc}</p>
              </div>
            ))}
          </div>
          <p className="text-sm text-muted-foreground mt-4">
            To set up a payment plan, <Link href="/contact" className="text-brand-600 hover:underline">contact us</Link> before booking.
          </p>
        </div>

        {/* Scholarships */}
        <div className="bg-blue-50 border border-blue-200 rounded-2xl p-8 mb-10">
          <h2 className="text-2xl font-bold mb-2">Scholarships & Bursaries</h2>
          <p className="text-muted-foreground mb-4">We believe financial circumstances should never stop anyone from accessing quality training.</p>
          <div className="grid sm:grid-cols-2 gap-4">
            {[
              { name: 'Youth Empowerment Bursary', desc: 'For Kenyans aged 18–25 with demonstrated financial need. Covers up to 50% of program fees.' },
              { name: 'Women in Tech Scholarship', desc: 'For women pursuing tech or digital marketing programs. Covers 30% of fees.' },
            ].map(s => (
              <div key={s.name} className="bg-white rounded-xl p-5 border border-blue-200">
                <h4 className="font-bold mb-1">{s.name}</h4>
                <p className="text-sm text-muted-foreground">{s.desc}</p>
              </div>
            ))}
          </div>
          <Link href="/contact" className="inline-flex items-center gap-2 mt-5 text-brand-600 font-semibold hover:gap-3 transition-all">
            Apply for Scholarship <ArrowRight className="w-4 h-4" />
          </Link>
        </div>

        {/* Add-ons */}
        <div>
          <h2 className="text-2xl font-bold mb-6">Optional Add-ons</h2>
          <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {addOns.map(a => (
              <div key={a.name} className="bg-card border border-border rounded-xl p-5">
                <h4 className="font-semibold text-sm mb-2">{a.name}</h4>
                <p className="text-lg font-black text-brand-600">{a.price}</p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </>
  );
}
