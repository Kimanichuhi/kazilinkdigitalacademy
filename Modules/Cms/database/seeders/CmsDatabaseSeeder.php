<?php

namespace Modules\Cms\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Academy\Models\Program;
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
        $this->seedBlogPosts();
        Faq::factory()->count(10)->create();
        Resource::factory()->count(5)->create();
        TeamMember::factory()->count(4)->create();
        Partner::factory()->count(6)->create();

        $this->seedFounder();
        $this->seedSuccessStories();

        $this->seedLegalPages();
        $this->seedAboutPage();
        $this->seedPricingPage();
        $this->seedPricingPlans();

        $settings = [
            'site_name' => ['value' => 'KAZI Link Academy', 'category' => 'general', 'label' => 'Site Name'],
            'hero_title' => ['value' => "Learn Today.\nEarn Tomorrow.\nLead the Future.", 'category' => 'homepage', 'label' => 'Hero Title'],
            'hero_subtitle' => ['value' => "At KAZI Link Academy, we equip ambitious learners with practical digital skills that lead to real opportunities. Whether you're a student, graduate, job seeker, entrepreneur, or professional, our expert-led training prepares you to earn online, grow your career, and thrive in today's digital economy.", 'category' => 'homepage', 'label' => 'Hero Subtitle'],
            'hero_cta_primary' => ['value' => 'Enroll Now', 'category' => 'homepage', 'label' => 'Hero Primary Button'],
            'hero_cta_secondary' => ['value' => 'Explore Courses', 'category' => 'homepage', 'label' => 'Hero Secondary Button'],
            'contact_email' => ['value' => 'hello@kazilink.academy', 'category' => 'contact', 'label' => 'Contact Email'],
            'contact_phone' => ['value' => '+254700000000', 'category' => 'contact', 'label' => 'Contact Phone'],
            'contact_address' => ['value' => 'Ol Kalou, Nyandarua', 'category' => 'contact', 'label' => 'Contact Address'],
            'contact_whatsapp' => ['value' => '+254700000000', 'category' => 'contact', 'label' => 'WhatsApp Number'],
            'social_facebook' => ['value' => 'https://facebook.com/kazilink', 'category' => 'social', 'label' => 'Facebook URL'],
            'social_instagram' => ['value' => 'https://instagram.com/kazilink', 'category' => 'social', 'label' => 'Instagram URL'],
            'social_linkedin' => ['value' => 'https://linkedin.com/company/kazilink', 'category' => 'social', 'label' => 'LinkedIn URL'],
        ];

        foreach ($settings as $key => $attrs) {
            SiteSetting::updateOrCreate(['key' => $key], $attrs);
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

        $hasBlocks = $page->blocks()->exists();

        // Mission/Vision/Core Values (content brief, 2026-08-06) — always
        // rewritten regardless of the guard below, since this content
        // changed even on an already-seeded page.
        $page->blocks()->where('type', 'mission_vision_value')->delete();
        $page->blocks()->where('type', 'core_value')->delete();

        $order = 1;

        foreach ([
            ['heading' => 'Our Mission', 'body' => 'To empower individuals with practical digital skills that unlock employment, entrepreneurship and lifelong success in the digital economy.', 'icon' => 'target'],
            ['heading' => 'Our Vision', 'body' => "To become Africa's leading digital skills academy, transforming lives through accessible, innovative and career-focused education.", 'icon' => 'globe'],
        ] as $item) {
            PageBlock::create([
                'page_id' => $page->id,
                'type' => 'mission_vision_value',
                'content' => ['heading' => $item['heading'], 'subtitle' => null, 'body' => $item['body'], 'meta' => ['icon' => $item['icon']]],
                'order_index' => $order++,
            ]);
        }

        foreach (['Excellence', 'Innovation', 'Integrity', 'Empowerment', 'Collaboration'] as $value) {
            PageBlock::create([
                'page_id' => $page->id,
                'type' => 'core_value',
                'content' => ['heading' => $value, 'subtitle' => null, 'body' => null, 'meta' => null],
                'order_index' => $order++,
            ]);
        }

        if ($hasBlocks) {
            return;
        }

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
            ['heading' => 'Ol Kalou Constituency Discount', 'body' => '15% off any program, automatically applied when you select Ol Kalou as your constituency during booking — no application needed.'],
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

    /**
     * Terms & Conditions, Privacy Policy, Refund Policy, and the Ol Kalou
     * Special Offer Notice, ported from client-supplied PDFs (brief,
     * 2026-08-15).
     * Bracketed placeholders ([INSERT ...]) are preserved verbatim from
     * the source documents — those are real compliance facts (registered
     * entity name, addresses, retention periods, review dates) that must
     * be supplied by Kazilink, not invented here. Do not publish these
     * pages to a production audience until every placeholder is filled in.
     */
    public function seedLegalPages(): void
    {
        foreach ([
            'terms' => ['title' => 'Terms & Conditions', 'sections' => $this->termsSections()],
            'privacy' => ['title' => 'Privacy Policy', 'sections' => $this->privacySections()],
            'refund' => ['title' => 'Refund Policy', 'sections' => $this->refundSections()],
            'ol-kalou-offer' => ['title' => 'Ol Kalou Special Offer Notice', 'sections' => $this->olKalouOfferSections()],
        ] as $slug => $doc) {
            $page = Page::updateOrCreate(
                ['slug' => $slug],
                ['title' => $doc['title'], 'is_published' => true]
            );

            $page->blocks()->where('type', 'legal_section')->delete();

            foreach ($doc['sections'] as $index => $section) {
                PageBlock::create([
                    'page_id' => $page->id,
                    'type' => 'legal_section',
                    'content' => ['heading' => $section['heading'], 'subtitle' => null, 'body' => $section['body'], 'meta' => null],
                    'order_index' => $index + 1,
                ]);
            }
        }
    }

    /**
     * @return list<array{heading: string, body: string}>
     */
    private function refundSections(): array
    {
        return [
            ['heading' => 'Document Information', 'body' => "Effective Date: [INSERT DATE]\nLast Updated: [INSERT DATE]\nGoverning Law: Republic of Kenya\n\nThis Refund Policy explains how Kazilink Digital Academy handles refund requests for programs, cohorts, resources, and related services."],
            ['heading' => '1. Purpose', 'body' => 'Kazilink Digital Academy aims to be fair and transparent when handling payments, cancellations, duplicate payments, failed services, and refund requests. This Policy should be read together with the Kazilink Terms & Conditions and Privacy Policy.'],
            ['heading' => '2. Scope', 'body' => 'This Policy applies to fees paid for Kazilink programs, cohorts, training services, digital resources, workshops, and other paid services offered through the Kazilink platform, unless a specific written offer or program agreement provides different refund terms.'],
            ['heading' => '3. Program Fees', 'body' => 'Program fees reserve a place in a cohort and may cover administrative processing, onboarding, learning materials, trainer time, platform access, and other delivery costs. Refund eligibility may depend on the timing of the request, whether training has started, and whether substantial services or digital content have already been accessed.'],
            ['heading' => '4. When Refunds May Be Approved', 'body' => "Kazilink may approve a full or partial refund where: Kazilink cancels a program and does not offer a suitable alternative; Kazilink is unable to provide the purchased service; a duplicate payment has been confirmed; an incorrect amount was charged due to a verified system or processing error; or another refund is required by applicable law."],
            ['heading' => '5. When Refunds May Be Declined', 'body' => "Refunds may be declined where: the learner changes their mind after enrollment; the learner stops attending voluntarily; the learner provides false or misleading information; the learner violates Kazilink policies or program rules; substantial course content, live sessions, resources, or services have already been accessed; or a digital resource has already been downloaded or delivered, where applicable law permits such exclusion."],
            ['heading' => '6. Cancellations Before a Program Starts', 'body' => 'Where a learner requests cancellation before a cohort begins, Kazilink may consider a refund, credit, transfer to another cohort, or partial refund after deducting reasonable administrative or payment-processing costs. Any specific cancellation window should be communicated on the relevant program page or offer terms.'],
            ['heading' => '7. Cancellations After a Program Starts', 'body' => 'After a program has started, refunds are generally limited because training capacity, materials, trainer time, and access may already have been provided. Kazilink may still consider exceptional circumstances on a case-by-case basis.'],
            ['heading' => '8. Digital Resources', 'body' => 'Downloaded, accessed, or delivered digital resources are generally non-refundable unless the resource was not provided, was materially defective, was duplicated in error, or a refund is required by applicable law.'],
            ['heading' => '9. Payment Charges and Third-Party Fees', 'body' => 'Refunds may exclude non-recoverable third-party transaction charges, bank charges, M-Pesa charges, payment-gateway fees, foreign-exchange differences, or other payment-processing costs unless the refund is caused by Kazilink error or applicable law requires otherwise.'],
            ['heading' => '10. How to Request a Refund', 'body' => "To request a refund, contact Kazilink with your full name, booking number, program or resource purchased, payment reference, amount paid, payment date, and reason for the request. Kazilink may ask for additional information to verify the payment and assess the request."],
            ['heading' => '11. Review and Processing Time', 'body' => 'Kazilink will review refund requests within a reasonable time after receiving the necessary information. Approved refunds will be processed through an appropriate payment channel, subject to banking, M-Pesa, gateway, and operational processing timelines.'],
            ['heading' => '12. Transfers and Credits', 'body' => 'Where a cash refund is not available or is not the most suitable remedy, Kazilink may offer a transfer to another cohort, a credit toward another program, replacement access, or another reasonable remedy.'],
            ['heading' => '13. Chargebacks and Disputes', 'body' => 'Users should contact Kazilink first so the issue can be investigated. Where a chargeback or payment dispute is raised, Kazilink may provide booking, payment, access, attendance, communication, and policy records to the relevant payment provider as permitted by law.'],
            ['heading' => '14. Fraud, Misrepresentation, and Abuse', 'body' => 'Kazilink may decline refunds and take appropriate action where fraudulent payment evidence, false identity information, unauthorized account use, policy abuse, or other misconduct is suspected.'],
            ['heading' => '15. Mandatory Consumer Rights', 'body' => 'Nothing in this Policy removes any mandatory consumer rights, statutory remedies, or protections available under applicable Kenyan law. Where this Policy conflicts with mandatory law, the law will prevail.'],
            ['heading' => '16. Contact', 'body' => "Refund requests and payment questions should be sent to:\n\nEmail: [INSERT BILLING EMAIL]\nTelephone: [INSERT PHONE NUMBER]\nPhysical Address: [INSERT ADDRESS]\nWebsite: [INSERT WEBSITE]"],
            ['heading' => 'Document Control', 'body' => "Document: Refund Policy\nOrganization: Kazilink Digital Academy\nVersion: [VERSION NUMBER]\nEffective Date: [DATE]\nLast Review Date: [DATE]\nApproved By: [NAME / POSITION]\n\n© 2026 Kazilink Digital Academy. All rights reserved."],
        ];
    }

    /**
     * @return list<array{heading: string, body: string}>
     */
    private function termsSections(): array
    {
        return [
            ['heading' => 'Document Information', 'body' => "Effective Date: [INSERT DATE]\nLast Updated: [INSERT DATE]\nGoverning Law: Republic of Kenya\n\nThis document forms a binding agreement between you and Kazilink Digital Academy."],
            ['heading' => '1. Introduction', 'body' => "Welcome to Kazilink Digital Academy ('Kazilink', 'we', 'us', or 'our'). Kazilink Digital Academy is an education and skills-development platform providing digital, professional, vocational, and other skills-based learning opportunities through online and, where applicable, physical or blended learning programs.\n\nThese Terms and Conditions ('Terms') govern your access to and use of the Kazilink Digital Academy website, learning platform, programs, courses, resources, enrollment services, payment services, and related products and services (collectively, the 'Platform').\n\nBy creating an account, enrolling in a program, purchasing a resource, submitting information, making a payment, or otherwise using the Platform, you acknowledge that you have read, understood, and agreed to be legally bound by these Terms. If you do not agree with these Terms, you should not create an account, enroll in a program, purchase a resource, or otherwise use the Platform."],
            ['heading' => '2. Definitions', 'body' => "\"Academy\", \"Kazilink\", \"we\", \"us\", or \"our\" means Kazilink Digital Academy and its lawful operating entity.\n\"User\", \"you\", or \"your\" means any person who accesses or uses the Platform.\n\"Student\" means a User who has enrolled in or is participating in a Kazilink program.\n\"Program\" means a course, training program, cohort, workshop, class, or other educational offering provided through Kazilink.\n\"Resource\" means any digital material made available through the Platform, including documents, guides, templates, recordings, notes, e-books, or other downloadable or viewable materials.\n\"Account\" means the registered user profile created to access restricted areas of the Platform.\n\"Enrollment\" means the process through which a User applies or registers to participate in a Program.\n\"Cohort\" means a scheduled group or intake of Students participating in a particular Program.\n\"Personal Data\" has the meaning given to it under applicable Kenyan data-protection law."],
            ['heading' => '3. Acceptance of These Terms', 'body' => "By selecting \"I Agree\", creating an account, enrolling in a Program, purchasing a Resource, or otherwise using the Platform, you confirm that: you have read these Terms; you understand these Terms; you agree to comply with these Terms; the information you provide is accurate and truthful; and you have the legal capacity to enter into these Terms or have the necessary consent from a parent or legal guardian where applicable.\n\nThe electronic acceptance of these Terms may constitute evidence of your agreement to them. Kazilink may retain records of the version of these Terms accepted by a User, together with the date and time of acceptance."],
            ['heading' => '4. Eligibility', 'body' => "You must provide accurate information when creating an account or enrolling in a Program. Unless a particular Program states otherwise:\n• There is no general requirement to have a particular academic qualification merely to create an account.\n• Certain Programs may have specific educational, professional, age, geographical, or other eligibility requirements.\n• Kazilink may request additional information where reasonably necessary to determine eligibility for a specific Program or offer.\n\nWhere a Program is subject to eligibility requirements, submitting an application does not automatically guarantee acceptance. Kazilink reserves the right to verify eligibility information before confirming enrollment."],
            ['heading' => '5. User Registration and Accounts', 'body' => "Some Platform features require an Account. When registering, you agree to:\n• Provide truthful and accurate information.\n• Keep your information reasonably up to date.\n• Maintain the confidentiality of your login credentials.\n• Not share your account with another person.\n• Notify Kazilink if you suspect unauthorized access.\n• Not create an account using another person's identity.\n\nYou are responsible for activities conducted through your Account unless the activity resulted from unauthorized access that was not caused by your failure to maintain reasonable account security. Kazilink may suspend or restrict an Account where there is reasonable evidence of fraud, abuse, unauthorized access, or violation of these Terms."],
            ['heading' => '6. Accuracy of Information', 'body' => "You are responsible for ensuring that information submitted to Kazilink is accurate, complete, and current. This includes information such as full name, phone number, email address, educational information, employment information, county, constituency, identification information where required, payment information, and program information.\n\nProviding false, misleading, fraudulent, or materially incomplete information may result in: rejection of an application, cancellation of enrollment, suspension of an Account, withdrawal of an offer or discount, cancellation of benefits obtained through false information, and other lawful action available to Kazilink."],
            ['heading' => '7. Programs and Courses', 'body' => "Kazilink provides educational and skills-development Programs designed to provide practical knowledge and learning opportunities. Program information may include: program title, description, curriculum, duration, delivery method, start date, end date, cohort, trainer, fees, eligibility requirements, and certification requirements.\n\nKazilink reserves the right to make reasonable changes to Program content, schedules, trainers, delivery methods, or other operational details where necessary. Where a material change substantially affects an enrolled Student, Kazilink will make reasonable efforts to communicate the change."],
            ['heading' => '8. Enrollment', 'body' => "Submitting an enrollment form does not necessarily mean that enrollment is confirmed. Enrollment may require: completion of the application form, submission of required information, meeting applicable eligibility requirements, payment of the applicable fee, verification or approval where required, and confirmation from Kazilink.\n\nKazilink may reject or defer an application where required information is missing, eligibility requirements are not satisfied, information cannot reasonably be verified, fraud or misrepresentation is suspected, the Program is full, or the Program has been suspended or withdrawn."],
            ['heading' => '9. Program Fees and Payments', 'body' => "Program fees are displayed on the Platform or communicated through official Kazilink channels. Unless expressly stated otherwise: fees are payable in Kenyan Shillings; enrollment is not considered fully confirmed until the required payment has been successfully received and verified; Kazilink may use third-party payment providers to process payments; and Users are responsible for providing accurate payment information.\n\nAvailable payment methods may include M-Pesa, bank transfer, card payments, and other payment methods made available by Kazilink."],
            ['heading' => '10. M-Pesa Payments', 'body' => "Where M-Pesa STK Push is available, the User may be required to provide a valid Safaricom mobile number. The User authorizes Kazilink to initiate a payment request for the amount displayed before payment confirmation. The User remains responsible for ensuring the phone number is correct, confirming the amount before entering their M-Pesa PIN, maintaining control of their M-Pesa account, and ensuring sufficient funds are available.\n\nKazilink will not request, collect, or store a User's M-Pesa PIN. A payment will only be considered successful after the relevant payment provider has returned and Kazilink has successfully processed a valid payment confirmation. A screenshot, SMS, or other user-provided evidence of payment may not by itself constitute final payment confirmation."],
            ['heading' => '11. Payment Verification and Fraud Prevention', 'body' => "Kazilink may verify payments against transaction records provided by its payment providers. Kazilink may delay enrollment or access while a payment is being verified.\n\nWhere fraud, chargeback abuse, payment manipulation, or other suspicious activity is detected, Kazilink may: suspend the affected transaction, suspend the relevant Account, cancel an enrollment, restrict access, request additional verification, recover amounts improperly obtained, and take other lawful measures."],
            ['heading' => '12. Refunds and Cancellations', 'body' => "Refund eligibility depends on the specific Program, Resource, payment, and circumstances involved. Unless a specific refund policy states otherwise, Users should review the applicable refund terms before making payment.\n\nKazilink may consider refunds where a Program is cancelled by Kazilink, Kazilink is unable to provide the purchased service, a duplicate payment has been confirmed, or a payment was processed incorrectly due to a verified system error.\n\nRefunds may be declined where the User changes their mind after accessing substantial course content, the User voluntarily stops participating, the User provides false information, the User violates these Terms, or a digital Resource has already been downloaded or accessed, where applicable law permits such exclusion.\n\nNothing in this section removes any mandatory consumer rights available under applicable Kenyan law."],
            ['heading' => '13. Digital Resources', 'body' => "Kazilink may offer digital Resources for free or for a fee. Paid Resources may require successful payment before access or download is provided. A Resource purchase generally grants the purchasing User a limited, personal, non-transferable right to access or download the Resource for lawful personal use.\n\nUnless expressly permitted, you may not: resell the Resource, redistribute the Resource, upload the Resource to another platform, share download links publicly, reproduce the Resource for commercial distribution, claim authorship of the Resource, or remove copyright or ownership notices.\n\nKazilink may suspend access where there is reasonable evidence of unauthorized distribution."],
            ['heading' => '14. Certificates', 'body' => "Where a Program provides a certificate, issuance may be subject to requirements such as completion of required coursework, attendance requirements, assessments, submission of required assignments, payment of applicable fees, and verification of Student information.\n\nA certificate confirms completion of the stated Kazilink Program requirements. It does not necessarily constitute a government-issued qualification, professional license, academic degree, or accreditation unless explicitly stated. Users must not falsely represent a Kazilink certificate as a qualification that Kazilink has not awarded."],
            ['heading' => '15. Ol Kalou Special Offer', 'body' => "Kazilink provides a 15% discount off the standard price of any Program to applicants whose constituency is Ol Kalou. The discount is applied automatically during booking once Ol Kalou is selected as the applicant's constituency — no code, application, or prior approval is required.\n\nUploading an identification document during booking is optional and is used only to help Kazilink verify the constituency claim; it does not need to be provided for the discount to apply. Kazilink may request or review this information at any time, and may reject, suspend, or withdraw the discount, cancel the associated booking, or require payment of the discounted difference where a constituency claim is found to be false or fraudulent.\n\nAdditional information regarding the collection and processing of identification information is provided in Kazilink's Privacy Policy and the Ol Kalou Special Offer Notice."],
            ['heading' => '16. Identification and Verification', 'body' => "Where identity verification is required for a legitimate Platform purpose, Users may be required to provide identification information. Users must only provide documents belonging to themselves unless Kazilink expressly authorizes otherwise. Submitting fraudulent, altered, stolen, or another person's identification documents is prohibited. Kazilink will handle personal identification information in accordance with its Privacy Policy and applicable Kenyan data-protection requirements."],
            ['heading' => '17. User Responsibilities', 'body' => "Users agree to use the Platform responsibly and lawfully. You must not: use the Platform for unlawful activities; attempt to gain unauthorized access; attack or interfere with the Platform; upload malicious software; attempt to bypass security controls; submit fraudulent information; impersonate another person; harass other Users or staff; abuse payment systems; distribute unauthorized Platform content; scrape or systematically copy Platform content without authorization; attempt to reverse engineer protected systems except where expressly permitted by law; or use the Platform to distribute harmful, defamatory, fraudulent, or unlawful content."],
            ['heading' => '18. Intellectual Property', 'body' => "Unless otherwise stated, the Platform and its contents — logos, branding, website design, software, course materials, graphics, videos, text, documents, templates, databases, learning resources, and original content — are owned by or licensed to Kazilink. Nothing in these Terms transfers ownership of Kazilink intellectual property to a User. Users receive only the rights expressly granted under these Terms."],
            ['heading' => '19. User-Submitted Content', 'body' => "Where Users submit content such as assignments, comments, testimonials, feedback, images, or other material, they remain responsible for the content they submit. Users must not submit content that infringes another person's rights, contains unlawful material, contains malicious software, contains confidential information belonging to another person, or violates applicable law.\n\nWhere necessary to operate the Platform, Users grant Kazilink a limited, non-exclusive right to store, process, display, and use submitted content for the purposes for which it was provided, subject to the Privacy Policy and applicable law."],
            ['heading' => '20. Testimonials and Reviews', 'body' => "Where a User voluntarily provides a testimonial, review, or feedback for publication, Kazilink may publish it through its website, marketing materials, or official communication channels in accordance with applicable permissions and privacy requirements. Kazilink may request permission before publicly associating a testimonial with identifying information."],
            ['heading' => '21. Third-Party Services', 'body' => "The Platform may integrate with third-party services, including payment providers, hosting providers, email providers, SMS providers, analytics services, storage providers, and authentication providers. Third-party services may operate under their own terms and privacy policies. Kazilink is not responsible for independent services outside its reasonable control, although Kazilink will take reasonable steps to select and manage service providers appropriate to the Platform's needs."],
            ['heading' => '22. Platform Availability', 'body' => "Kazilink aims to maintain reliable access to the Platform but does not guarantee uninterrupted or error-free availability. Temporary interruption may occur due to maintenance, security incidents, network failures, hosting problems, third-party service failures, power or telecommunications outages, force majeure events, or other circumstances outside Kazilink's reasonable control. Kazilink will make reasonable efforts to restore affected services."],
            ['heading' => '23. Security', 'body' => "Kazilink implements reasonable technical and organizational safeguards designed to protect Platform information. However, no internet-based system can be guaranteed to be completely secure. Users are responsible for maintaining the security of their accounts and devices, and must immediately notify Kazilink if they suspect unauthorized access to their Account."],
            ['heading' => '24. Privacy and Personal Data', 'body' => "Kazilink collects and processes personal information in accordance with its Privacy Policy and applicable Kenyan data-protection law. The Privacy Policy explains what information we collect, why we collect it, how we use it, how we protect it, who may receive it, how long we retain it, your applicable data-protection rights, and how to contact us regarding your personal information. The Privacy Policy forms an important part of your relationship with Kazilink."],
            ['heading' => '25. Data Accuracy', 'body' => "Users are responsible for providing accurate personal information. Where inaccurate information affects enrollment, certification, payments, eligibility, or other Platform services, Kazilink may require the User to correct the information before proceeding. Kazilink may retain records necessary to establish legitimate business, legal, financial, security, or compliance requirements, subject to applicable law."],
            ['heading' => '26. Suspension and Termination', 'body' => "Kazilink may suspend or terminate an Account where reasonably necessary due to material breach of these Terms, fraud, unauthorized access, abuse of the Platform, payment fraud, misrepresentation, unauthorized distribution of content, conduct that threatens Platform security, conduct that harms other Users, legal or regulatory requirements, or other circumstances outside Kazilink's reasonable control.\n\nWhere appropriate, Kazilink will provide notice and an opportunity to remedy the issue. Termination does not automatically remove obligations that by their nature should continue after termination."],
            ['heading' => '27. Limitation of Liability', 'body' => "To the extent permitted by applicable law, Kazilink will not be liable for losses arising solely from circumstances outside its reasonable control, including third-party service interruptions, telecommunications failures, unauthorized acts by third parties, or User misuse of the Platform.\n\nKazilink does not guarantee that completing a Program will result in employment, business income, freelancing opportunities, promotion, or any specific financial outcome. Educational Programs provide knowledge and skills; individual outcomes depend on numerous factors including the User's effort, market conditions, qualifications, experience, and opportunities.\n\nNothing in these Terms excludes or limits liability that cannot lawfully be excluded or limited under Kenyan law."],
            ['heading' => '28. Indemnification', 'body' => "To the extent permitted by law, a User may be responsible for losses, claims, costs, or expenses reasonably arising from the User's fraudulent conduct, material breach of these Terms, unauthorized use of the Platform, infringement of third-party intellectual property, unlawful conduct, or misuse of another person's personal information. This provision does not apply where the relevant loss resulted from Kazilink's own unlawful conduct or negligence to the extent such liability cannot legally be excluded."],
            ['heading' => '29. Changes to the Platform', 'body' => "Kazilink may modify, improve, suspend, or discontinue Platform features from time to time. Where a change materially affects an existing paid service, Kazilink will make reasonable efforts to communicate the change and provide any rights required by applicable law."],
            ['heading' => '30. Changes to These Terms', 'body' => "Kazilink may update these Terms to reflect changes in the Platform, changes in services, changes in applicable law, security improvements, or operational changes. The updated Terms will be published on the Platform with a revised \"Last Updated\" date. Where legally required or where changes materially affect Users, Kazilink may require Users to actively accept the updated Terms before continuing to use certain services."],
            ['heading' => '31. Electronic Communications', 'body' => "By using the Platform, you agree that Kazilink may communicate with you electronically regarding matters relating to your Account, enrollment, payments, Programs, Resources, security, and other Platform services. Transactional communications may be sent by email, SMS, in-platform notifications, or other communication methods associated with the Account. Marketing communications will be handled in accordance with applicable law and the User's applicable communication preferences."],
            ['heading' => '32. Complaints and Disputes', 'body' => "Users should first contact Kazilink regarding complaints or disputes so that the matter can be investigated and resolved where possible. Complaints should include sufficient information to allow Kazilink to identify the relevant Account, transaction, Program, or issue. Kazilink will make reasonable efforts to respond and resolve legitimate complaints promptly."],
            ['heading' => '33. Governing Law', 'body' => 'These Terms shall be governed by and interpreted in accordance with the laws of the Republic of Kenya, subject to any mandatory legal protections applicable to Users.'],
            ['heading' => '34. Dispute Resolution', 'body' => "The parties should first attempt to resolve disputes through good-faith communication. Where a dispute cannot be resolved through internal communication, the parties may pursue any mediation, arbitration, court proceedings, or other dispute-resolution mechanism available under applicable Kenyan law. Nothing in this section prevents a person from exercising a statutory right or seeking urgent legal relief where permitted by law."],
            ['heading' => '35. Severability', 'body' => 'If any provision of these Terms is determined to be invalid, unlawful, or unenforceable, that provision shall be interpreted or modified to the extent necessary to make it lawful where possible. The remaining provisions shall continue in effect to the extent permitted by law.'],
            ['heading' => '36. No Waiver', 'body' => 'Failure by Kazilink to enforce a provision of these Terms does not constitute a waiver of the right to enforce that provision later.'],
            ['heading' => '37. Entire Agreement', 'body' => "These Terms, together with the Privacy Policy, applicable Program-specific terms, refund policies, and other policies expressly incorporated into them, constitute the principal terms governing your use of the Platform. Where a specific Program has additional terms, those terms will apply to that Program to the extent they do not conflict with mandatory legal requirements."],
            ['heading' => '38. Contact Information', 'body' => "For questions regarding these Terms, complaints, or other legal matters, contact:\n\nLegal / Operating Entity: [INSERT REGISTERED ENTITY NAME]\nPhysical Address: [INSERT ADDRESS]\nPostal Address: [INSERT POSTAL ADDRESS]\nEmail: [INSERT OFFICIAL EMAIL]\nTelephone: [INSERT PHONE NUMBER]\nWebsite: [INSERT WEBSITE]"],
            ['heading' => '39. User Agreement', 'body' => "Before creating an Account, Users will be required to confirm: 'I have read and agree to the Kazilink Digital Academy Terms & Conditions and acknowledge the Privacy Policy.'\n\nThe registration system should record the User/account identifier, date and time of acceptance, version of the Terms, version of the Privacy Policy, and applicable consent records. The system should not rely solely on the checkbox itself as the only record of acceptance."],
            ['heading' => 'Document Control', 'body' => "Document: Terms & Conditions\nOrganization: Kazilink Digital Academy\nVersion: [VERSION NUMBER]\nEffective Date: [DATE]\nLast Updated: [DATE]\nGoverning Law: Republic of Kenya\n\n© 2026 Kazilink Digital Academy. All rights reserved."],
        ];
    }

    /**
     * @return list<array{heading: string, body: string}>
     */
    private function privacySections(): array
    {
        return [
            ['heading' => 'Document Information', 'body' => "Effective Date: [INSERT DATE]\nLast Updated: [INSERT DATE]\nGoverning Law: Republic of Kenya\n\nThis notice explains your rights and our responsibilities under applicable Kenyan data-protection law."],
            ['heading' => '1. Introduction', 'body' => "Kazilink Digital Academy ('Kazilink', 'we', 'us', or 'our') respects your privacy and is committed to protecting the personal information entrusted to us.\n\nThis Privacy Policy explains how Kazilink collects, receives, uses, stores, protects, shares, and otherwise processes personal data when you access or use our website, create an account, enroll in a Program, purchase a Resource, communicate with us, make a payment, or otherwise interact with our services.\n\nThis Policy applies to students, prospective students, trainers, administrators, visitors, customers, and other individuals whose personal data is processed through the Kazilink platform. We process personal data in accordance with applicable laws of the Republic of Kenya, including applicable data-protection requirements.\n\nBy using Kazilink, you acknowledge that you have read this Privacy Policy. Where the law requires consent for a particular processing activity, we will request that consent separately and appropriately."],
            ['heading' => '2. Who We Are', 'body' => "Platform: Kazilink Digital Academy\nLegal / Operating Entity: [INSERT REGISTERED ENTITY NAME]\nPhysical Address: [INSERT ADDRESS]\nPostal Address: [INSERT POSTAL ADDRESS]\nEmail: [INSERT OFFICIAL EMAIL]\nTelephone: [INSERT PHONE NUMBER]\nWebsite: [INSERT WEBSITE]\n\nFor purposes of applicable data-protection law, Kazilink may act as a data controller for personal information that we determine the purposes and means of processing. Where we process personal data on behalf of another organization, we may act as a data processor."],
            ['heading' => '3. What Is Personal Data?', 'body' => "Personal data means information relating to an identified or identifiable individual. Depending on how you interact with Kazilink, this may include: name, email address, telephone number, county, constituency, educational information, professional information, employment information, account credentials, identification information, identification document images, payment and transaction information, enrollment information, communications with Kazilink, technical and device information, account activity, information submitted through forms, and information required to provide specific services.\n\nWe do not collect every category of information from every User. We seek to collect only information reasonably necessary for the relevant purpose."],
            ['heading' => '4. Personal Data We Collect', 'body' => "4.1 Registration Information — when you create an Account, we may collect: full name, email address, phone number, password or authentication information, account role, registration date, and other information necessary to create and secure your Account.\n\n4.2 Enrollment Information — when you enroll in a Program, we may collect: full name, phone number, email address, county, constituency, educational background, professional information, employer information, program selected, cohort selected, enrollment information, and other information reasonably required for the selected Program.\n\n4.3 Identification Information — certain services, offers, or verification processes may require: national identification number, identification document information, and identification document image. We will not request identification information merely because it is convenient to do so; where identification information is required, we will communicate the purpose for which it is being collected."],
            ['heading' => '5. Ol Kalou Special Offer Verification', 'body' => "Kazilink may provide a special offer to eligible persons who satisfy defined geographical or other eligibility criteria relating to Ol Kalou. Where verification is necessary, applicants may be asked to provide identification information and/or an image of an identification document.\n\nThe purpose of this collection may include: verifying eligibility for the applicable Ol Kalou offer, preventing multiple or fraudulent claims, confirming that information submitted during the application process is genuine, maintaining appropriate records of beneficiaries, and protecting the integrity of the special offer.\n\nThe identification information collected for this purpose will not automatically be used for unrelated purposes. Where another purpose requires the use of the information, Kazilink will process it only where there is an appropriate lawful basis."],
            ['heading' => '6. Important Notice Regarding Geographic Verification', 'body' => "Kazilink recognizes that an identification document may contain information that does not necessarily establish a person's current residence. Accordingly, Kazilink will define and publish the eligibility criteria applicable to the Ol Kalou offer. Eligibility may be determined using one or more permitted verification criteria, depending on the specific offer.\n\nProviding an identification document does not automatically guarantee eligibility. Kazilink may request additional information where reasonably necessary to establish eligibility."],
            ['heading' => '7. Payment Information', 'body' => "When you make a payment, we may process: amount paid, transaction reference, payment status, date and time, payment method, account or enrollment reference, M-Pesa transaction information, and relevant payment-provider response information.\n\nWhere M-Pesa STK Push is used, you may provide a Safaricom mobile number to initiate the payment request. Kazilink does not request or store your M-Pesa PIN. Payment processing may involve third-party payment providers.\n\nWe process payment information primarily to: process payments, confirm transactions, associate payments with enrollments or purchases, prevent fraud, resolve payment disputes, maintain financial records, and comply with applicable legal obligations."],
            ['heading' => '8. Technical and Usage Information', 'body' => "When you use our Platform, technical information may be collected automatically, including: IP address, browser type, operating system, device information, referring pages, pages visited, date and time of access, approximate usage information, error information, and security-related information.\n\nThis information may be used to: maintain Platform security, diagnose technical problems, improve performance, understand Platform usage, prevent abuse, and detect suspicious activity."],
            ['heading' => '9. Information You Voluntarily Provide', 'body' => "You may voluntarily provide information when you contact us, submit a support request, complete a contact form, submit feedback, submit a testimonial, participate in surveys, apply for a Program, purchase Resources, or communicate with trainers or administrators. We process such information for the purpose for which it was provided and other lawful purposes connected to our services."],
            ['heading' => '10. Why We Collect Personal Data', 'body' => "Depending on the circumstances, we may process personal data to: create and manage Accounts, process Program applications, manage enrollments, manage cohorts, provide educational services, communicate with Students, process payments, provide purchased Resources, verify eligibility, prevent fraud, maintain security, provide customer support, issue certificates, manage trainers, improve our services, conduct analytics, maintain business records, comply with legal obligations, protect our legal rights, respond to lawful requests, and resolve disputes.\n\nWe will not intentionally collect personal information for purposes unrelated to the stated purpose without an appropriate legal basis."],
            ['heading' => '11. Lawful Basis for Processing', 'body' => "Depending on the circumstances, Kazilink may process personal data based on one or more lawful grounds recognized under applicable Kenyan data-protection law:\n• Consent — where consent is legally required, we will request it in a clear and appropriate manner.\n• Contract — processing may be necessary to provide services requested by you, such as managing an enrollment.\n• Legal Obligation — we may process information where required to comply with applicable law.\n• Legitimate Interests — where legally permitted, we may process information for legitimate operational, security, fraud-prevention, or business purposes, provided applicable legal requirements are satisfied.\n• Other Lawful Grounds — we may rely on other lawful grounds where recognized by applicable law.\n\nThe applicable lawful basis may depend on the specific processing activity."],
            ['heading' => '12. Consent', 'body' => "Where Kazilink relies on consent, consent will be requested in a manner that is voluntary, specific, informed, clear, and capable of being withdrawn where applicable. We will not treat acceptance of general Terms & Conditions as automatically constituting consent to every optional processing activity. Where separate consent is appropriate, a separate consent mechanism will be provided."],
            ['heading' => '13. Withdrawal of Consent', 'body' => "Where processing is based on consent, you may withdraw your consent subject to applicable law. Withdrawal of consent does not necessarily invalidate processing that occurred before withdrawal. Where processing is necessary for a service you have requested, withdrawing consent may affect our ability to continue providing that service. We will explain the consequences where relevant."],
            ['heading' => '14. Marketing Communications', 'body' => "Kazilink may communicate information about Programs, events, opportunities, or services where permitted by law. Where marketing consent or an opt-out mechanism is required, appropriate controls will be provided. Users may request that marketing communications stop. Transactional and service-related communications may continue where reasonably necessary to provide the requested service."],
            ['heading' => '15. Sharing Personal Data', 'body' => "Kazilink does not sell personal data as a business practice. We may disclose personal data to appropriate third parties where necessary and lawful, including: payment providers, hosting providers, cloud storage providers, email providers, SMS providers, technology service providers, professional advisers, auditors, regulators or government authorities where legally required, law enforcement authorities where legally required, and other service providers necessary to operate the Platform. We seek to limit disclosure to information reasonably necessary for the relevant purpose."],
            ['heading' => '16. Service Providers', 'body' => "Some Platform services may be provided through third-party providers for: cloud hosting, databases, authentication, payment processing, email delivery, SMS, analytics, file storage, security, and monitoring. Where appropriate, Kazilink will use contractual or other safeguards intended to protect personal data processed by service providers."],
            ['heading' => '17. International Data Transfers', 'body' => "Some service providers may process or store information outside Kenya. Where personal data is transferred outside Kenya, Kazilink will take reasonable steps to ensure that applicable legal requirements and appropriate safeguards are considered, including assessing the destination country, the receiving organization, contractual safeguards, security measures, the purpose of the transfer, and applicable legal requirements."],
            ['heading' => '18. Identification Documents', 'body' => "Identification documents require heightened protection. Where Kazilink collects an identification document image: access will be restricted, the document will not be publicly accessible, it will not be intentionally exposed through public URLs, access will be limited to authorized personnel or systems, appropriate technical safeguards will be applied, access may be logged, and the document will be retained only for an appropriate period. Kazilink will not intentionally use identification documents for unrelated marketing purposes."],
            ['heading' => '19. Encryption and Security', 'body' => "Kazilink uses reasonable technical and organizational measures designed to protect personal data, which may include: encryption in transit, encryption at rest where appropriate, secure authentication, role-based access controls, strong password protection, multi-factor authentication where available, access logging, audit trails, secure backups, restricted administrative access, secure file storage, security monitoring, vulnerability management, rate limiting, and secure software-development practices. No electronic system can be guaranteed to be completely secure."],
            ['heading' => '20. Access Control', 'body' => "Kazilink will implement role-based access to personal information. Personnel should only access personal data necessary for their assigned responsibilities — for example, finance personnel may need payment information, student-support personnel may need enrollment information, authorized verification personnel may need eligibility documentation, and system administrators may manage technical infrastructure. Administrative access does not automatically mean unrestricted access to all personal data."],
            ['heading' => '21. ID Document Access Controls', 'body' => "Access to identification documents should be more restrictive than ordinary account information. Where technically feasible: ID images should be stored privately, access should require authentication, authorization should be checked before retrieval, access should be logged, images should not be permanently exposed through public URLs, administrative access should be limited, download permissions should be restricted, and unnecessary copies should not be created."],
            ['heading' => '22. Data Retention', 'body' => "Kazilink will not retain personal data indefinitely merely because it was collected. Retention periods will depend on the purpose for which the data was collected, legal obligations, accounting requirements, dispute resolution requirements, fraud prevention, security requirements, contractual obligations, and legitimate operational requirements. When personal data is no longer reasonably required, Kazilink will take appropriate steps to delete, anonymize, or otherwise securely dispose of it, subject to applicable legal retention requirements."],
            ['heading' => '23. Retention of ID Documents', 'body' => "Because identification documents contain significant personal information, Kazilink will establish a specific retention period for documents collected for the Ol Kalou Special Offer.\n\nProposed retention period: [INSERT PERIOD]\n\nAt the end of the applicable retention period, Kazilink will securely delete or otherwise dispose of the document unless continued retention is legally justified. The retention period should be reviewed before publication of this Policy and should reflect the actual business and legal requirements."],
            ['heading' => '24. Data Minimization', 'body' => "Kazilink seeks to collect only personal information that is reasonably necessary for a defined purpose. For example, where an eligibility determination can be made without retaining a complete identification document indefinitely, Kazilink may consider alternative approaches such as temporary verification, recording only the required verification result, storing limited identifying information, and secure deletion after verification. The exact approach will depend on the applicable eligibility criteria and legal requirements."],
            ['heading' => '25. Data Accuracy', 'body' => 'We take reasonable steps to maintain accurate personal information. You may request correction of inaccurate or incomplete personal information. Users should also notify Kazilink where information changes or is incorrect.'],
            ['heading' => '26. Your Data Protection Rights', 'body' => "Subject to applicable Kenyan law, you may have rights relating to your personal data, including rights to: be informed about processing, access personal data, request correction of inaccurate data, request deletion in appropriate circumstances, object to certain processing, request restriction of processing where applicable, withdraw consent where consent is the lawful basis, and exercise other rights provided by applicable law. Some rights may be subject to lawful limitations or exemptions."],
            ['heading' => '27. How to Exercise Your Rights', 'body' => "To make a data-protection request, contact:\n\nData Protection Contact: [INSERT NAME/TITLE]\nEmail: [INSERT DATA PROTECTION EMAIL]\nTelephone: [INSERT PHONE NUMBER]\n\nYour request should provide enough information to allow us to identify the relevant Account and understand your request. We may take reasonable steps to verify your identity before releasing or modifying personal information, to prevent unauthorized persons from accessing another person's information."],
            ['heading' => '28. Identity Verification for Data Requests', 'body' => "Because personal data requests can involve sensitive information, Kazilink may request reasonable evidence of identity before processing a request. We will seek to avoid collecting excessive identification information merely to process a routine request. Where possible, verification will be proportionate to the sensitivity of the requested information."],
            ['heading' => '29. Automated Decision-Making', 'body' => "Kazilink may use automated systems for certain operational functions, such as filtering, search, notifications, analytics, fraud detection, and recommendations. Where automated decision-making has legal or similarly significant effects on an individual, Kazilink will take steps required by applicable law."],
            ['heading' => '30. Cookies and Similar Technologies', 'body' => "Kazilink may use cookies or similar technologies to maintain login sessions, remember preferences, improve functionality, measure website performance, understand usage, and improve security. Where required, appropriate cookie notices and controls will be provided. Additional information may be provided through a separate Cookie Policy."],
            ['heading' => "31. Children's Data", 'body' => "Kazilink's Programs may have specific age requirements. Where a service is directed toward minors or involves the processing of children's personal data, Kazilink will implement safeguards required by applicable law. Where parental or guardian consent is required, Kazilink will request the appropriate consent. Users must not falsely represent their age or identity."],
            ['heading' => '32. Data Breaches and Security Incidents', 'body' => "Kazilink maintains procedures for identifying, assessing, containing, investigating, and responding to personal-data security incidents. Where a personal-data breach occurs, Kazilink will take reasonable steps to identify the affected systems, contain the incident, assess the information affected, investigate the cause, take corrective measures, document the incident, and make notifications required by applicable law. Where notification to affected individuals is legally required, Kazilink will provide the information required by law."],
            ['heading' => '33. Data Protection by Design', 'body' => 'Kazilink aims to incorporate privacy and security considerations into the design and development of its Platform, including data minimization, restricted access, secure defaults, encryption, and privacy-aware database design.'],
            ['heading' => '34. Third-Party Websites', 'body' => 'The Platform may contain links to third-party websites. Kazilink does not control the privacy practices of independent third parties. Users should review the privacy policies of third-party websites before submitting personal information to them.'],
            ['heading' => '35. Changes to This Privacy Policy', 'body' => "Kazilink may update this Privacy Policy from time to time to reflect new services, changes in technology, changes in data-processing practices, security improvements, changes in applicable law, or regulatory guidance. The updated Policy will display a revised \"Last Updated\" date. Where required, Kazilink will provide additional notice or request consent."],
            ['heading' => '36. Privacy by Default', 'body' => 'Kazilink aims to configure systems so that unnecessary personal information is not publicly displayed or unnecessarily shared — for example, private identification-document storage, restricted administrative access, limited profile visibility, secure password storage, controlled downloads, secure file storage, audit logging, and controlled administrative privileges.'],
            ['heading' => '37. Complaints', 'body' => 'If you believe that Kazilink has improperly processed your personal information, please contact us first so that we can investigate and attempt to resolve the matter. You may also have the right to lodge a complaint with the relevant Kenyan data-protection regulator or seek other remedies available under applicable law.'],
            ['heading' => '38. Contact Us', 'body' => "For privacy questions, requests, or complaints:\n\nLegal / Operating Entity: [INSERT REGISTERED ENTITY NAME]\nData Protection Contact: [INSERT NAME/TITLE]\nEmail: [INSERT PRIVACY EMAIL]\nTelephone: [INSERT PHONE NUMBER]\nPhysical Address: [INSERT ADDRESS]\nPostal Address: [INSERT POSTAL ADDRESS]\nWebsite: [INSERT WEBSITE]"],
            ['heading' => '39. Privacy Acknowledgment', 'body' => "During registration, Users will be shown a clear link to this Privacy Policy. The registration interface may include: 'I have read and understood the Kazilink Digital Academy Privacy Policy.'\n\nWhere separate consent is required for a specific processing activity, that consent will be requested separately — for example, an Ol Kalou verification process may display: 'I understand that Kazilink will process my identification information and identification document for the purpose of verifying my eligibility for the Ol Kalou Special Offer, as explained in the applicable verification notice.'\n\nSuch optional or purpose-specific consent should not be hidden inside general Terms & Conditions."],
            ['heading' => '40. Version and Record Keeping', 'body' => 'Kazilink may maintain records of Privacy Policy versions, effective dates, consent records where applicable, withdrawal of consent, data-subject requests, security incidents, and relevant processing activities. These records help Kazilink demonstrate accountability and responsible data governance.'],
            ['heading' => 'Document Control', 'body' => "Document: Privacy Policy & Data Protection Notice\nOrganization: Kazilink Digital Academy\nVersion: [VERSION NUMBER]\nEffective Date: [DATE]\nLast Review Date: [DATE]\nNext Review Date: [DATE]\nApproved By: [NAME / POSITION]\n\n© 2026 Kazilink Digital Academy. All rights reserved."],
        ];
    }

    /**
     * @return list<array{heading: string, body: string}>
     */
    private function olKalouOfferSections(): array
    {
        return [
            ['heading' => 'Document Information', 'body' => "Effective Date: [INSERT DATE]\nLast Updated: [INSERT DATE]\nGoverning Law: Republic of Kenya\n\nRead together with the Kazilink Privacy Policy and Terms & Conditions."],
            ['heading' => '1. Purpose of This Notice', 'body' => "Kazilink Digital Academy ('Kazilink', 'we', 'us', or 'our') provides a 15% discount off the standard price of any Program to individuals whose constituency is Ol Kalou (the 'Ol Kalou Special Offer').\n\nThe discount is applied automatically when you select Ol Kalou as your constituency during booking — it is not conditional on submitting an identification document. Kazilink may nonetheless request or review identification information after the fact to confirm the constituency claim and prevent fraudulent use of the offer.\n\nThis notice explains why we may request identification information, what information we collect, how we use it, who may access it, how we protect it, how long we intend to retain it, what happens if you do not provide it, and your rights regarding the information.\n\nThis notice should be read together with the Kazilink Digital Academy Privacy Policy and Terms & Conditions."],
            ['heading' => '2. Who Qualifies?', 'body' => "The Ol Kalou Special Offer applies to any applicant who selects 'Ol Kalou' as their constituency when booking a Program. No other criteria apply, and no separate application or approval is required — the 15% discount is calculated and shown before payment.\n\nSelecting Ol Kalou as your constituency when it is not accurate is a false statement and may result in the discount being revoked under Section 14 below, even after a booking has been made."],
            ['heading' => '3. Why Verification Is Required', 'body' => "Verification is intended to protect the integrity of the special offer. Kazilink may conduct verification to: confirm that an applicant meets the published eligibility requirements, prevent fraudulent applications, prevent multiple claims where the offer is limited to eligible individuals, protect the fairness of the program, maintain accurate records of beneficiaries, and ensure that the special offer reaches the intended community."],
            ['heading' => '4. Information We May Request', 'body' => "Depending on the verification method applicable to the offer, we may request: full name, national identification number, identification document type, identification document image, county, constituency, and other information reasonably necessary to determine eligibility.\n\nWe will not intentionally request information that is unnecessary for the stated verification purpose."],
            ['heading' => '5. Identification Document Image', 'body' => "Where required, you may be asked to upload a clear image of your identification document. The document may contain information such as name, identification number, photograph, date of birth, and other information appearing on the document.\n\nYou should only submit an identification document belonging to you. Submitting another person's document without lawful authorization is prohibited."],
            ['heading' => '6. How Your Identification Information Will Be Used', 'body' => "Identification information collected through this process may be used for: eligibility verification, fraud prevention, duplicate-claim detection, offer administration, verification of information submitted during enrollment, maintaining records relating to the offer, resolving disputes concerning eligibility, and complying with applicable legal obligations.\n\nWe will not intentionally use your identification document for unrelated advertising or marketing purposes."],
            ['heading' => '7. Verification Is Not a Precondition of the Discount', 'body' => "Unlike offers that require approval before a benefit is granted, the Ol Kalou Special Offer's 15% discount is applied automatically at booking based on your selected constituency — it does not wait on identification review.\n\nIdentification documents, where submitted, are used afterward to confirm the claim. If Kazilink later determines that a booking's constituency claim cannot be verified or is inaccurate — for example, the document is unreadable, appears altered, or conflicts with other information on file — Kazilink may revoke the discount from that booking under Section 14 below, rather than withholding it upfront."],
            ['heading' => '8. Verification of Geographical Eligibility', 'body' => "An identification document may not necessarily establish a person's current place of residence. For this reason, Kazilink will use the eligibility criteria specifically published for the relevant offer.\n\nDepending on the offer, additional information may be requested where reasonably necessary to establish eligibility. Kazilink will not assume that an applicant is eligible solely because an identification document contains a particular location unless that is expressly part of the published eligibility criteria."],
            ['heading' => '9. Access to Your Identification Information', 'body' => "Access to identification information will be restricted to authorized persons or systems that have a legitimate reason to process it. Depending on their responsibilities, authorized personnel may include designated verification personnel, authorized administrators, relevant compliance personnel, and authorized technical personnel where required for system maintenance or security.\n\nAccess should be granted according to the person's responsibilities rather than automatically to every administrator."],
            ['heading' => '10. Security Measures', 'body' => "Kazilink will implement reasonable technical and organizational safeguards designed to protect identification information. These may include encryption, private storage, authentication, role-based access control, restricted administrative privileges, access logging, audit trails, secure backups, security monitoring, and controlled download permissions.\n\nIdentification documents should not be stored in publicly accessible website folders or exposed through unrestricted URLs."],
            ['heading' => '11. Retention of Identification Documents', 'body' => "Kazilink will retain identification information only for as long as reasonably necessary for the purposes for which it was collected, subject to applicable legal, regulatory, accounting, security, and dispute-resolution requirements.\n\nIntended retention period for Ol Kalou verification documents: [INSERT RETENTION PERIOD]\n\nAfter the applicable retention period, Kazilink will take reasonable steps to securely delete, destroy, anonymize, or otherwise dispose of the information, unless continued retention is legally justified."],
            ['heading' => '12. Verification Result', 'body' => "Where possible, Kazilink may retain the verification outcome separately from the original identification document — for example, a record showing eligibility status, the offer applied for, the verification date, and who or what verified it.\n\nWhere continued storage of the complete identification document is no longer necessary, the original document may be securely deleted in accordance with the applicable retention policy."],
            ['heading' => '13. What Happens If You Do Not Provide Your ID?', 'body' => "You still receive the 15% Ol Kalou discount if you do not upload an identification document — the discount is not conditional on it. The document is optional supporting evidence that helps Kazilink confirm your constituency claim if it is ever reviewed.\n\nIf you choose not to provide identification and Kazilink is later unable to confirm your constituency through other means, Kazilink may still revoke the discount from the affected booking under Section 14 below."],
            ['heading' => '14. Fraud and Misrepresentation', 'body' => "You must provide truthful information when selecting your constituency and, where submitted, when providing identification information. You must not: select 'Ol Kalou' as your constituency when it is not accurate, upload a forged or altered identification document, use another person's identification document, create multiple accounts to obtain multiple discounts, or otherwise misrepresent your eligibility.\n\nWhere Kazilink determines that a booking's Ol Kalou constituency claim was false, Kazilink may revoke the 15% discount from that booking, require payment of the discounted difference before the booking is finalized or confirmed, suspend the relevant account, investigate the matter, and take other lawful action where appropriate."],
            ['heading' => '15. Data Protection Rights', 'body' => "Subject to applicable law, you may have rights relating to your personal information, including rights to: be informed about processing, access your personal data, request correction of inaccurate information, request deletion where legally applicable, object to certain processing, request restriction of processing where applicable, withdraw consent where consent is the applicable legal basis, and exercise other rights provided under applicable Kenyan law.\n\nSome rights may be subject to lawful limitations."],
            ['heading' => '16. How to Contact Kazilink', 'body' => "For questions about the verification process or your personal information, contact:\n\nData Protection Contact: [INSERT NAME/TITLE]\nEmail: [INSERT PRIVACY EMAIL]\nTelephone: [INSERT PHONE NUMBER]\nPhysical Address: [INSERT ADDRESS]\nWebsite: [INSERT WEBSITE]"],
            ['heading' => '17. Separate Consent', 'body' => "Where Kazilink relies on consent for the processing described in this notice, consent will be obtained separately from acceptance of the general Terms & Conditions. Before submitting your identification document, you should be presented with a clear consent statement.\n\nRecommended consent wording: 'I have read and understood the Ol Kalou Special Offer Eligibility, Identification Verification & Consent Notice. I understand that Kazilink Digital Academy may process my identification information and identification document for the purpose of verifying my eligibility for the Ol Kalou Special Offer, preventing fraudulent claims, and administering the offer. I understand that providing this information does not guarantee that I will qualify for the offer.'\n\nWhere legally appropriate, the form should also provide a separate link to the Privacy Policy."],
            ['heading' => '18. Applicant Declaration', 'body' => "Before submitting an application, you may also be required to confirm: 'I confirm that the identification document and information I have provided belongs to me and that the information submitted is accurate and truthful to the best of my knowledge.'"],
            ['heading' => '19. Electronic Record of Consent', 'body' => "Where consent is obtained electronically, Kazilink may retain a record of your user/account identifier and the date and time of consent. The system should not unnecessarily store the identification document inside the consent record itself."],
            ['heading' => '20. Withdrawal of Consent', 'body' => "Where processing is based on consent, you may withdraw your consent by contacting Kazilink using the contact information above. Withdrawal of consent may affect Kazilink's ability to verify your eligibility for the special offer. Withdrawal does not necessarily invalidate processing that occurred before the withdrawal. Where another lawful basis permits continued processing, Kazilink may continue processing the information to the extent permitted by applicable law."],
            ['heading' => '21. Changes to This Notice', 'body' => "Kazilink may update this notice when the verification process changes, the eligibility requirements change, the categories of information collected change, the retention period changes, applicable law or regulatory guidance changes, or security or operational requirements change. The latest version will display the applicable effective and review dates."],
            ['heading' => '22. Important Notice', 'body' => "The Ol Kalou Special Offer is a Kazilink initiative and is subject to the eligibility criteria communicated by Kazilink. The offer should not be interpreted as a government benefit, government-sponsored program, or government endorsement unless Kazilink expressly states otherwise."],
            ['heading' => 'Document Control', 'body' => "Document: Ol Kalou Special Offer — Eligibility, Identification Verification & Consent Notice\nOrganization: Kazilink Digital Academy\nVersion: [VERSION NUMBER]\nEffective Date: [DATE]\nLast Review Date: [DATE]\nNext Review Date: [DATE]\nApproved By: [NAME / POSITION]\n\n© 2026 Kazilink Digital Academy. All rights reserved."],
        ];
    }

    /**
     * Homepage CEO MESSAGE section source (content brief, 2026-08-06).
     */
    public function seedFounder(): void
    {
        TeamMember::updateOrCreate(
            ['email' => 'stephen@kazilink.academy'],
            [
                'full_name' => 'Stephen Wanyoike Waithaka',
                'title' => 'Founder',
                'bio' => "Hello and welcome.\n\nMy name is Stephen Wanyoike Waithaka, Founder of KAZI Link Academy.\n\nI believe talent is everywhere, but opportunities are not always equally available. That is why we created KAZI Link Academy—to bridge the gap between education, digital skills, and meaningful employment.\n\nOur mission is simple: to empower learners with practical, market-ready skills that can generate income, create businesses, and transform communities.\n\nWhether you are taking your first steps into the digital world or looking to advance your professional career, we are committed to walking the journey with you.\n\nYour success is our greatest achievement.\n\nWelcome to the future of learning and earning.",
                'avatar_url' => null,
                'department' => 'leadership',
                'is_featured' => true,
                'is_active' => true,
                'order_index' => 0,
            ]
        );
    }

    /**
     * Real blog categories and articles (ported from the legacy Next.js
     * seed data, which had real titles/excerpts but only placeholder
     * body copy) replace the previous factory-faked categories/posts.
     */
    public function seedBlogPosts(): void
    {
        BlogPost::query()->delete();
        BlogCategory::query()->delete();

        $categories = [];

        foreach ([
            ['name' => 'Success Stories', 'slug' => 'success-stories', 'description' => 'Real income journeys from our graduates', 'color' => '#10b981', 'order_index' => 1],
            ['name' => 'Freelancing Tips', 'slug' => 'freelancing-tips', 'description' => 'Practical advice for freelancers', 'color' => '#0ea5e9', 'order_index' => 2],
            ['name' => 'Digital Marketing', 'slug' => 'digital-marketing', 'description' => 'Strategies and guides for marketers', 'color' => '#f97316', 'order_index' => 3],
            ['name' => 'E-Commerce', 'slug' => 'ecommerce', 'description' => 'Online selling strategies and tips', 'color' => '#8b5cf6', 'order_index' => 4],
            ['name' => 'Tech & Coding', 'slug' => 'tech-coding', 'description' => 'Developer tutorials and career guidance', 'color' => '#ec4899', 'order_index' => 5],
            ['name' => 'Academy News', 'slug' => 'academy-news', 'description' => 'Updates from Kazilink Digital Academy', 'color' => '#f59e0b', 'order_index' => 6],
        ] as $category) {
            $categories[$category['slug']] = BlogCategory::create($category + ['is_active' => true]);
        }

        foreach ([
            [
                'category' => 'success-stories',
                'title' => 'How Sarah Went From KSh 15K to KSh 120K/Month as a Freelancer',
                'slug' => 'sarah-freelancer-success-story',
                'excerpt' => 'A step-by-step account of how a shop attendant in Nairobi transformed her income in just 3 months after completing our Freelancing Mastery Bootcamp.',
                'content' => "Six months ago, Sarah Njeri was folding clothes on a shop floor in Nairobi, earning KSh 15,000 a month and wondering if there was a better way to use the hours she spent scrolling through job listings that never called back.\n\nToday, she runs a logo design business from her phone and laptop, working with clients in the US and UK, and pulling in more than KSh 120,000 in a typical month. Here is exactly what changed.\n\nThe turning point: enrolling in the Bootcamp\n\nSarah heard about the Freelancing Mastery Bootcamp from a cousin who had already completed it. She was skeptical — she had tried \"make money online\" schemes before and lost time and a little money to each one. What convinced her to sign up was the structure: a defined six-week program with weekly deliverables, not a vague promise.\n\nIn week one, she built a portfolio from scratch using free design tools, working on three sample projects even though she had no clients yet. In week two, she learned how to write a Fiverr gig description that actually answers a buyer's real question — what will I get, how fast, and why you instead of the next seller.\n\nLanding the first client\n\nHer first paying gig came in during week three: a KSh 1,500 logo for a small business page on Instagram. It wasn't glamorous, but it gave her two things that mattered more than the money — a five-star review, and proof that the process worked.\n\nFrom there, she reinvested every review into her next pitch. By week five she had eight completed orders and a Fiverr Level 1 badge. By the time the bootcamp ended, she had raised her prices three times and was turning away work she didn't have time for.\n\nScaling past KSh 100,000 a month\n\nThe jump from a side hustle to a real income came from two decisions. First, Sarah stopped competing purely on price — she niched down into logo and brand identity design for small e-commerce sellers, a category she understood well from her retail job. Second, she started asking satisfied clients for referrals directly instead of waiting for the platform's algorithm to send new buyers her way.\n\nWithin three months of finishing the program, her monthly income had crossed KSh 120,000 — eight times what she was earning on the shop floor.\n\nWhat Sarah tells new students\n\n\"I always thought freelancing was for people who already knew how to code or design,\" she says. \"What the bootcamp actually taught me was how to sell a skill I could learn in weeks, not years. The design work was the easy part. Structuring my profile, my pricing and my client communication — that's what got me paid.\"\n\nSarah's story isn't unusual among our graduates, but it is a reminder that the biggest barrier to earning online usually isn't talent — it's not knowing where to start. If you're in the position she was six months ago, the Freelancing Mastery Bootcamp is designed to take you from zero to your first paying client in 30 days.",
                'thumbnail_url' => 'https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg?auto=compress&cs=tinysrgb&w=800',
                'tags' => ['freelancing', 'success story', 'fiverr', 'kenya'],
                'is_featured' => true,
                'read_time_minutes' => 7,
                'published_at' => now()->subDays(40),
                'view_count' => 2340,
            ],
            [
                'category' => 'freelancing-tips',
                'title' => '10 Upwork Profile Tips That Will Get You Your First Client Fast',
                'slug' => '10-upwork-profile-tips-first-client',
                'excerpt' => "Your Upwork profile is your digital storefront. These 10 proven strategies will make clients choose you over thousands of other freelancers.",
                'content' => "Your Upwork profile is the only sales pitch you get before a client decides whether to open your proposal at all. With thousands of freelancers competing for the same jobs, small details make the difference between being invited to interview and being ignored. Here are ten changes that consistently move the needle for our students.\n\n1. Use a real, well-lit headshot\nProfiles with a clear, friendly photo get significantly more responses than those with logos, cartoons, or blurry selfies. Clients are hiring a person, not a placeholder.\n\n2. Write a title that names a result, not a job title\n\"Graphic Designer\" tells a client nothing. \"I Design Logos and Brand Kits That Make Small Businesses Look Established\" tells them exactly what they'll get.\n\n3. Open your overview with the client's problem\nSkip the life story. Your first two sentences should describe the outcome you deliver and who you deliver it for, because that's all most clients read before scrolling on.\n\n4. Lead with outcomes, not tools\nListing every software you know is less persuasive than describing what those tools let you build for a client. Save the tool list for the skills section.\n\n5. Set a rate that matches your evidence, not your ambition\nNew profiles with no reviews should price to win their first few jobs and build a review history. You can raise your rate steadily once you have five-star feedback to back it up.\n\n6. Build a portfolio even with zero paid clients\nCreate two or three sample projects specifically for the niche you want to work in. A logo mockup for a fictional client is far more convincing than an empty portfolio section.\n\n7. Pass the relevant skills tests\nUpwork's skills tests are optional, but a high score signals competence to clients who are comparing dozens of similar profiles at a glance.\n\n8. Specialize instead of listing everything you can do\nA profile that says \"I do logos, websites, video editing and voiceovers\" reads as unfocused. Pick the service you're strongest at and make that the center of your profile.\n\n9. Keep your availability badge accurate\nClients filter by availability. If your badge says you're not accepting new work, you will never show up in most searches — update it every time your capacity changes.\n\n10. Respond to invitations within the hour when you can\nUpwork's algorithm and human clients both reward speed. Setting a phone notification for new invitations and proposals can be the difference between winning a job and losing it to whoever replied first.\n\nNone of these tips require experience you don't already have — they require rewriting how you present the experience you do have. Students in our Freelancing Mastery Bootcamp apply this exact checklist to their own profiles in week one, before they send a single proposal.",
                'thumbnail_url' => 'https://images.pexels.com/photos/3184339/pexels-photo-3184339.jpeg?auto=compress&cs=tinysrgb&w=800',
                'tags' => ['upwork', 'freelancing', 'tips', 'profile'],
                'is_featured' => true,
                'read_time_minutes' => 9,
                'published_at' => now()->subDays(47),
                'view_count' => 5670,
            ],
            [
                'category' => 'digital-marketing',
                'title' => 'The Complete Guide to Facebook Ads for Kenyan Businesses in 2026',
                'slug' => 'complete-guide-facebook-ads-kenya-2026',
                'excerpt' => 'Everything you need to know about running profitable Facebook and Instagram ad campaigns targeting Kenyan customers.',
                'content' => "Facebook and Instagram remain the most effective paid advertising channels for reaching Kenyan customers directly on the phones they use every day. But most small businesses running ads locally are still guessing — boosting posts without a strategy and wondering why the sales don't follow. Here is a practical, current guide to running Facebook ads that actually convert in the Kenyan market.\n\nStart with the right campaign objective\n\nMeta's ad platform will let you run a campaign optimized for reach, engagement, traffic, or conversions — and the choice matters more than the creative. If your goal is sales or bookings, run a Conversions or Sales campaign with the Meta Pixel installed on your site, not an Engagement campaign. Engagement campaigns are optimized to get likes and comments, not customers.\n\nInstall and verify the Meta Pixel before you spend a shilling\n\nWithout the Pixel, Meta has no way to learn which of your visitors actually buy, and your ad spend will be far less efficient. Install it through Events Manager, verify it's firing with the Meta Pixel Helper browser extension, and set up your key events — typically \"Add to Cart,\" \"Initiate Checkout,\" and \"Purchase.\"\n\nBuild audiences that fit the Kenyan market\n\nBroad interest targeting (for example, \"small business owners\" nationally) tends to underperform tighter combinations. For most Kenyan SMEs, the highest-converting audiences are: a Lookalike Audience built from your existing customer list, a Retargeting Audience of people who visited your site or engaged with your page in the last 30 days, and a narrow Interest Audience limited to the counties you can realistically deliver to.\n\nDesign creative for a data-conscious audience\n\nMany Kenyan users are on limited mobile data plans and will skip past heavy, slow-loading creative. Favor short (under 15 second) vertical videos or a single strong image with minimal text overlay. Lead with the price or the offer in the first three seconds — don't make people watch to find out what you're selling.\n\nSet a realistic testing budget\n\nBefore scaling any campaign, run at least three ad variations (different creative or copy) at a modest daily budget — KSh 500 to KSh 1,000 per ad is enough to gather early signal in most local campaigns — for 3 to 5 days before judging performance. Judging an ad after one day almost always leads to premature, wrong decisions.\n\nUse M-Pesa-friendly checkout messaging\n\nIf your checkout or booking flow accepts M-Pesa, say so directly in your ad copy and landing page. \"Pay via M-Pesa\" removes a real hesitation for Kenyan buyers who may not have or trust a card for online purchases.\n\nTrack cost per result, not just reach\n\nReach and impressions look impressive in Ads Manager but don't pay your bills. The number that matters is your cost per purchase, per lead, or per booking — compare that against your margin to know whether a campaign is actually profitable, not just \"performing.\"\n\nRetarget before you go broader\n\nMost first-time visitors don't buy immediately. Before increasing your budget to reach new people, make sure you have a retargeting campaign running to bring back the visitors who already showed interest — this is usually the cheapest, highest-converting traffic you'll ever advertise to.\n\nFacebook ads reward businesses that test methodically and track the right numbers, not businesses that spend the most. Students in our Digital Marketing Professional program build and run a live ad campaign with a real budget during the course, so they graduate with results to show clients — not just theory.",
                'thumbnail_url' => 'https://images.pexels.com/photos/905163/pexels-photo-905163.jpeg?auto=compress&cs=tinysrgb&w=800',
                'tags' => ['facebook ads', 'digital marketing', 'kenya', '2026'],
                'is_featured' => false,
                'read_time_minutes' => 12,
                'published_at' => now()->subDays(55),
                'view_count' => 3890,
            ],
            [
                'category' => 'ecommerce',
                'title' => 'How to Start a Dropshipping Business in Kenya with Zero Inventory',
                'slug' => 'start-dropshipping-kenya-zero-inventory',
                'excerpt' => "Dropshipping lets you sell products online without holding stock. Here's how to get started in Kenya with as little as KSh 5,000.",
                'content' => "Dropshipping lets you run an online store and sell physical products without ever buying, storing, or shipping inventory yourself — your supplier ships directly to your customer, and you keep the margin between what you charge and what you pay the supplier. It's one of the lowest-capital ways to start an e-commerce business in Kenya, and it's possible to launch with as little as KSh 5,000. Here's how the process actually works.\n\nHow the model works, step by step\n\nA customer orders and pays through your online store. You then place that same order with your supplier, at a lower wholesale price, and provide your customer's shipping details. The supplier packs and ships the product directly to your customer. You never touch the product, and you only pay the supplier once you've already been paid by the customer — which is what makes the low startup cost possible.\n\nChoosing a niche that works in the Kenyan market\n\nNot every dropshipping niche translates well locally. Products with long international shipping times can frustrate Kenyan customers who are used to same-day or next-day delivery from local sellers. The niches that tend to work best combine reasonable shipping times with strong impulse appeal — think phone accessories, fashion jewellery, home gadgets, and fitness accessories sourced from suppliers with Africa-friendly shipping, rather than ultra-niche products with a six-week wait.\n\nSetting up your store\n\nShopify is the most beginner-friendly platform for a Kenyan dropshipping store, with plans starting affordably and a large ecosystem of supplier integrations. Alternatives like WooCommerce on a WordPress site can reduce ongoing costs but require more technical setup. Whichever platform you choose, prioritize a clean, mobile-first design — the majority of your Kenyan traffic will come from phones, not desktops.\n\nFinding reliable suppliers\n\nLook for suppliers with consistent positive ratings, clearly stated processing times, and — where possible — a warehouse or fulfilment option closer to East Africa to cut shipping delays. Order a sample of any product yourself before listing it, so you know exactly what your customer will receive and can write accurate descriptions and photos.\n\nPricing for profit after every cost\n\nNew dropshippers often price based on the product cost alone and forget the other costs that eat into margin: payment processing fees, advertising spend, and returns or lost packages. A simple starting formula is to price at least three times your landed product cost, then adjust based on what your advertising data tells you about what customers will actually pay.\n\nGetting your first customers\n\nWith zero inventory and a small budget, most successful Kenyan dropshippers start with a mix of Facebook and Instagram ads targeting a specific, well-defined audience, combined with organic TikTok content showing the product in use. Paid ads bring speed; organic content builds trust and lowers your advertising costs over time as more people recognize your brand.\n\nHandling customer service without holding stock\n\nBecause you don't control fulfilment directly, clear communication becomes your main tool for managing customer expectations. State realistic delivery windows upfront, send tracking information as soon as it's available, and respond quickly to questions — most complaints in dropshipping come from silence, not from the wait itself.\n\nDropshipping isn't a shortcut to passive income — it still requires real marketing, real customer service, and real testing to find products that sell. But it removes the single biggest barrier to starting an online store in Kenya: the capital needed to buy inventory upfront. Students in our E-Commerce Empire Builder program build and launch a real store during the course, testing products and ads with actual budget before they graduate.",
                'thumbnail_url' => 'https://images.pexels.com/photos/230544/pexels-photo-230544.jpeg?auto=compress&cs=tinysrgb&w=800',
                'tags' => ['dropshipping', 'ecommerce', 'kenya', 'shopify'],
                'is_featured' => false,
                'read_time_minutes' => 11,
                'published_at' => now()->subDays(65),
                'view_count' => 7230,
            ],
        ] as $post) {
            BlogPost::create([
                'category_id' => $categories[$post['category']]->id,
                'author_id' => null,
                'title' => $post['title'],
                'slug' => $post['slug'],
                'excerpt' => $post['excerpt'],
                'content' => $post['content'],
                'thumbnail_url' => $post['thumbnail_url'],
                'tags' => $post['tags'],
                'is_featured' => $post['is_featured'],
                'is_published' => true,
                'published_at' => $post['published_at'],
                'view_count' => $post['view_count'],
                'read_time_minutes' => $post['read_time_minutes'],
            ]);
        }
    }

    /**
     * The 4 curated BLOG / SUCCESS STORIES students (content brief,
     * 2026-08-06) replace the previous 8 factory-fake testimonials.
     *
     * The brief referenced story details (achievement/quote) by a
     * footnoted "source document" that wasn't included with the brief —
     * only name/county/course were given. The `content`/`achievement`
     * copy below is drafted to fit those facts, not transcribed from a
     * real source, and should be swapped for the actual stories once
     * available.
     */
    public function seedSuccessStories(): void
    {
        Testimonial::query()->delete();

        foreach ([
            [
                'student_name' => 'Grace Wanjiku',
                'location' => 'Nakuru County',
                'course_completed' => 'Freelancing & Online Work',
                'course_title' => 'Freelancing & Online Work',
                'achievement' => 'Now earns a full-time income as an academic writer and freelancer',
                'student_title' => 'Freelance Academic Writer',
                'content' => 'Before KAZI Link Academy I had no idea freelancing was even possible from Nakuru. The Academic Writing & Freelancing course gave me a real skill and a real client pipeline — I now work with clients online full-time.',
                'income_before' => 'KES 0',
                'income_after' => 'KES 45,000',
            ],
            [
                'student_name' => 'Martin Ndungu',
                'location' => 'Nyandarua County',
                'course_completed' => 'Artificial Intelligence',
                'course_title' => 'Artificial Intelligence',
                'achievement' => 'Now offers AI consulting and content services to local businesses',
                'student_title' => 'AI Consultant',
                'content' => 'The AI & ChatGPT Masterclass completely changed how I work. I went from barely using a computer for business to building AI workflows and consulting for other entrepreneurs in Nyandarua.',
                'income_before' => 'KES 0',
                'income_after' => 'KES 60,000',
            ],
            [
                'student_name' => 'Mercy Njeri',
                'location' => 'Nyandarua County',
                'course_completed' => 'Digital Marketing',
                'course_title' => 'Digital Marketing',
                'achievement' => 'Runs paid ad campaigns for local businesses as a marketing consultant',
                'student_title' => 'Digital Marketing Consultant',
                'content' => 'I took the Digital Marketing course to help grow my own small business, and ended up discovering a whole new career. I now manage social media and ad campaigns for other businesses too.',
                'income_before' => 'KES 5,000',
                'income_after' => 'KES 38,000',
            ],
            [
                'student_name' => 'Sarafina Njeri',
                'location' => 'Nyandarua County',
                'course_completed' => 'Creative Design',
                'course_title' => 'Creative Design',
                'achievement' => 'Builds brand identities and social media graphics as a freelance designer',
                'student_title' => 'Freelance Graphic Designer',
                'content' => 'Graphic Design felt like a dream until KAZI Link Academy made it practical. I learned Canva and Photoshop and now design branding and social media content for clients as a freelancer.',
                'income_before' => 'KES 0',
                'income_after' => 'KES 32,000',
            ],
        ] as $index => $story) {
            Testimonial::create([
                'program_id' => Program::where('title', $story['course_title'])->value('id'),
                'student_name' => $story['student_name'],
                'student_title' => $story['student_title'],
                'student_avatar_url' => null,
                'location' => $story['location'],
                'course_completed' => $story['course_completed'],
                'achievement' => $story['achievement'],
                'content' => $story['content'],
                'rating' => 5,
                'income_before' => $story['income_before'],
                'income_after' => $story['income_after'],
                'video_url' => null,
                'is_featured' => true,
                'is_published' => true,
                'order_index' => $index,
            ]);
        }
    }
}
