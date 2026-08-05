<?php

namespace Modules\Cms\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Cms\Models\BlogCategory;
use Modules\Cms\Models\BlogPost;
use Modules\Cms\Models\Faq;
use Modules\Cms\Models\NavItem;
use Modules\Cms\Models\NavMenu;
use Modules\Cms\Models\Page;
use Modules\Cms\Models\PageBlock;
use Modules\Cms\Models\Partner;
use Modules\Cms\Models\PricingPlan;
use Modules\Cms\Models\Resource;
use Modules\Cms\Models\SiteSetting;
use Modules\Cms\Models\TeamMember;
use Modules\Cms\Models\Testimonial;

class CmsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogCategories = BlogCategory::factory()->count(4)->create();
        BlogPost::factory()
            ->count(6)
            ->recycle($blogCategories)
            ->create();
        Testimonial::factory()->count(8)->create();
        Faq::factory()->count(10)->create();
        Resource::factory()->count(5)->create();
        TeamMember::factory()->count(4)->create();
        Partner::factory()->count(6)->create();

        foreach ([
            ['title' => 'Terms & Conditions', 'slug' => 'terms'],
            ['title' => 'Privacy Policy', 'slug' => 'privacy'],
            ['title' => 'Refund Policy', 'slug' => 'refund'],
        ] as $page) {
            Page::firstOrCreate(
                ['slug' => $page['slug']],
                ['title' => $page['title'], 'is_published' => true]
            );
        }

        $this->seedAboutPage();
        $this->seedPricingPage();
        $this->seedPricingPlans();

        $settings = [
            'site_name' => ['value' => 'Kazilink Digital Academy', 'category' => 'general', 'label' => 'Site Name'],
            'contact_email' => ['value' => 'hello@kazilink.academy', 'category' => 'contact', 'label' => 'Contact Email'],
            'contact_phone' => ['value' => '+254700000000', 'category' => 'contact', 'label' => 'Contact Phone'],
            'contact_address' => ['value' => 'Westlands, Nairobi, Kenya', 'category' => 'contact', 'label' => 'Contact Address'],
            'contact_whatsapp' => ['value' => '+254700000000', 'category' => 'contact', 'label' => 'WhatsApp Number'],
            'social_facebook' => ['value' => 'https://facebook.com/kazilink', 'category' => 'social', 'label' => 'Facebook URL'],
            'social_instagram' => ['value' => 'https://instagram.com/kazilink', 'category' => 'social', 'label' => 'Instagram URL'],
            'social_linkedin' => ['value' => 'https://linkedin.com/company/kazilink', 'category' => 'social', 'label' => 'LinkedIn URL'],
        ];

        foreach ($settings as $key => $attrs) {
            SiteSetting::firstOrCreate(['key' => $key], $attrs);
        }

        $headerMenu = NavMenu::firstOrCreate(
            ['location' => 'header'],
            ['name' => 'Header Menu', 'is_active' => true]
        );

        $items = [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'About', 'url' => '/about'],
            ['label' => 'Programs', 'url' => '/programs'],
            ['label' => 'Cohorts', 'url' => '/cohorts'],
            ['label' => 'Pricing', 'url' => '/pricing'],
            ['label' => 'Success Stories', 'url' => '/success-stories'],
            ['label' => 'Blog', 'url' => '/blog'],
            ['label' => 'Resources', 'url' => '/resources'],
            ['label' => 'FAQ', 'url' => '/faq'],
            ['label' => 'Contact', 'url' => '/contact'],
        ];

        foreach ($items as $index => $item) {
            NavItem::firstOrCreate(
                ['menu_id' => $headerMenu->id, 'label' => $item['label']],
                ['url' => $item['url'], 'order_index' => $index, 'is_active' => true, 'target' => '_self']
            );
        }
    }

    public function seedAboutPage(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'about'],
            ['title' => 'About Us', 'is_published' => true]
        );

        if ($page->blocks()->exists()) {
            return;
        }

        $order = 0;

        PageBlock::create([
            'page_id' => $page->id,
            'type' => 'hero',
            'content' => [
                'heading' => 'Empowering Kenyans to Earn Online',
                'subtitle' => 'We equip students with practical, income-generating digital skills — from freelancing to e-commerce.',
                'body' => null,
                'meta' => null,
            ],
            'order_index' => $order++,
        ]);

        foreach ([
            ['heading' => 'Our Mission', 'body' => 'To make practical digital income skills accessible to every Kenyan, regardless of background.', 'icon' => 'target'],
            ['heading' => 'Our Vision', 'body' => 'A Kenya where anyone with an internet connection can build a sustainable online income.', 'icon' => 'globe'],
            ['heading' => 'Our Values', 'body' => 'Practical over theoretical. Results over certificates. Community over competition.', 'icon' => 'award'],
        ] as $item) {
            PageBlock::create([
                'page_id' => $page->id,
                'type' => 'mission_vision_value',
                'content' => ['heading' => $item['heading'], 'subtitle' => null, 'body' => $item['body'], 'meta' => ['icon' => $item['icon']]],
                'order_index' => $order++,
            ]);
        }

        PageBlock::create([
            'page_id' => $page->id,
            'type' => 'story',
            'content' => [
                'heading' => 'Our Story',
                'subtitle' => null,
                'body' => "Kazilink Digital Academy started with a simple observation: thousands of Kenyans had smartphones and internet access, but no clear path to turning that connectivity into income.\n\nWe built a training model focused entirely on outcomes — every program is designed around a specific way to earn, taught by trainers who do the work themselves, not just teach it.\n\nSince then we've grown into a full academy spanning freelancing, digital marketing, e-commerce, and more — with a track record of real income results, not just completion certificates.\n\nToday, our mission remains the same: turn digital skills into real, measurable income for every student who walks through our doors.",
                'meta' => null,
            ],
            'order_index' => $order++,
        ]);

        foreach ([
            ['year' => '2021', 'title' => 'Founded', 'description' => 'Kazilink Digital Academy launched with our first freelancing cohort.'],
            ['year' => '2022', 'title' => 'First 500 Students', 'description' => 'Crossed our first major enrollment milestone.'],
            ['year' => '2022', 'title' => 'E-commerce Program Launched', 'description' => 'Expanded beyond freelancing into online selling.'],
            ['year' => '2023', 'title' => 'Digital Marketing Track', 'description' => 'Added a dedicated digital marketing specialization.'],
            ['year' => '2023', 'title' => '2,000 Students Trained', 'description' => 'Reached 2,000 cumulative graduates.'],
            ['year' => '2024', 'title' => 'Corporate Partnerships', 'description' => 'Began partnering with employers for graduate placement.'],
            ['year' => '2025', 'title' => 'Nationwide Reach', 'description' => 'Students enrolled from every county in Kenya.'],
        ] as $milestone) {
            PageBlock::create([
                'page_id' => $page->id,
                'type' => 'timeline_item',
                'content' => ['heading' => $milestone['title'], 'subtitle' => $milestone['year'], 'body' => $milestone['description'], 'meta' => null],
                'order_index' => $order++,
            ]);
        }
    }

    public function seedPricingPage(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'pricing'],
            ['title' => 'Pricing', 'is_published' => true]
        );

        if ($page->blocks()->exists()) {
            return;
        }

        $order = 0;

        PageBlock::create([
            'page_id' => $page->id,
            'type' => 'hero',
            'content' => [
                'heading' => 'Simple, Transparent Pricing',
                'subtitle' => 'Invest in skills that pay for themselves — flexible plans for every budget.',
                'body' => null,
                'meta' => null,
            ],
            'order_index' => $order++,
        ]);

        foreach ([
            ['heading' => 'Pay in Full', 'body' => 'Pay the full program fee upfront and get a 5% discount.'],
            ['heading' => '2 Installments', 'body' => 'Split your fee across 2 payments — 60% to start, 40% at the midpoint.'],
            ['heading' => '3 Installments', 'body' => 'Split your fee across 3 monthly payments with no extra charge.'],
        ] as $option) {
            PageBlock::create([
                'page_id' => $page->id,
                'type' => 'installment_option',
                'content' => ['heading' => $option['heading'], 'subtitle' => null, 'body' => $option['body'], 'meta' => null],
                'order_index' => $order++,
            ]);
        }

        foreach ([
            ['heading' => 'Merit Scholarship', 'body' => 'Up to 50% off for top-performing students from our entry assessment.'],
            ['heading' => 'Community Scholarship', 'body' => 'Discounted rates for students referred by partner community organizations.'],
        ] as $scholarship) {
            PageBlock::create([
                'page_id' => $page->id,
                'type' => 'scholarship',
                'content' => ['heading' => $scholarship['heading'], 'subtitle' => null, 'body' => $scholarship['body'], 'meta' => null],
                'order_index' => $order++,
            ]);
        }

        foreach ([
            ['name' => '1-on-1 Mentorship (per month)', 'price' => 3000],
            ['name' => 'Portfolio Review', 'price' => 1500],
            ['name' => 'Job Placement Support', 'price' => 5000],
            ['name' => 'Extended Access (+3 months)', 'price' => 2000],
        ] as $addon) {
            PageBlock::create([
                'page_id' => $page->id,
                'type' => 'addon',
                'content' => ['heading' => $addon['name'], 'subtitle' => null, 'body' => null, 'meta' => ['price' => $addon['price']]],
                'order_index' => $order++,
            ]);
        }
    }

    public function seedPricingPlans(): void
    {
        if (PricingPlan::exists()) {
            return;
        }

        foreach ([
            [
                'name' => 'Starter', 'tag' => null, 'price' => 15000, 'period' => 'program',
                'description' => 'Perfect for exploring a single skill track.',
                'features' => ['1 program of your choice', 'Access to course materials', 'Community forum access', 'Certificate of completion'],
                'is_highlighted' => false, 'order_index' => 1,
            ],
            [
                'name' => 'Professional', 'tag' => 'Most Popular', 'price' => 35000, 'period' => 'program',
                'description' => 'Our most popular plan for serious career changers.',
                'features' => ['1 program of your choice', 'Live trainer sessions', '1-on-1 mentorship (1 session/month)', 'Job placement support', 'Certificate of completion'],
                'is_highlighted' => true, 'order_index' => 2,
            ],
            [
                'name' => 'Enterprise', 'tag' => null, 'price' => 80000, 'period' => 'program',
                'description' => 'For teams and organizations upskilling multiple staff.',
                'features' => ['Up to 5 team members', 'Any program track', 'Dedicated account manager', 'Custom cohort scheduling', 'Job placement support'],
                'is_highlighted' => false, 'order_index' => 3,
            ],
        ] as $plan) {
            PricingPlan::create([
                'name' => $plan['name'],
                'tag' => $plan['tag'],
                'price' => $plan['price'],
                'currency' => 'KES',
                'period' => $plan['period'],
                'description' => $plan['description'],
                'features' => $plan['features'],
                'cta_text' => 'Get Started',
                'cta_link' => '/booking',
                'is_highlighted' => $plan['is_highlighted'],
                'is_published' => true,
                'order_index' => $plan['order_index'],
            ]);
        }
    }
}
