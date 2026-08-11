<?php

namespace Modules\Academy\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Academy\Models\Cohort;
use Modules\Academy\Models\Program;
use Modules\Academy\Models\ProgramCategory;
use Modules\Academy\Models\Trainer;

/**
 * The 6 real KAZI Link Academy courses (content brief, 2026-08-06) — one
 * course per category, matching the site's simplified category=course
 * catalog structure. Curriculum modules come from the brief's own named
 * module lists (no sub-lesson breakdown was given, so each module is a
 * single curriculum entry). "Skills after course" not explicitly listed
 * for Courses 2-6 in the brief were derived directly from that course's
 * own module names, not invented independently.
 */
class AcademyDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = Trainer::count() > 0 ? Trainer::all() : Trainer::factory()->count(5)->create();

        // Retire any previously-seeded categories/programs that aren't
        // part of the 6 real courses (e.g. earlier Faker-generated demo
        // data) — cascades to their cohorts/bookings, which is fine for
        // this pre-launch demo dataset.
        $realNames = collect($this->courses())->pluck('category')->all();
        // forceDelete (not delete): Program is soft-deleted, and a plain
        // delete() wouldn't trip the cohorts/bookings cascadeOnDelete FK,
        // leaving stale cohorts/bookings pointing at a hidden program.
        Program::withTrashed()->whereNotIn('title', $realNames)->get()->each->forceDelete();
        ProgramCategory::whereNotIn('name', $realNames)->delete();

        foreach ($this->courses() as $data) {
            $category = ProgramCategory::updateOrCreate(
                ['name' => $data['category']],
                [
                    'slug' => Str::slug($data['category']),
                    'description' => $data['outcome'],
                    'icon' => $data['icon'],
                    'color' => $data['color'],
                    'order_index' => $data['order'],
                    'is_active' => true,
                ]
            );

            $program = Program::updateOrCreate(
                ['title' => $data['category']],
                [
                    'category_id' => $category->id,
                    'slug' => Str::slug($data['category']),
                    'subtitle' => $data['outcome'],
                    'description' => $data['outcome'].' Delivered by expert-led, hands-on training built around real career opportunities.',
                    'short_description' => $data['outcome'],
                    'thumbnail_url' => null,
                    'gallery_urls' => [],
                    'duration_weeks' => 8,
                    'duration_label' => '8 Weeks',
                    'level' => $data['level'],
                    'delivery_mode' => 'online',
                    'price' => $data['price'],
                    'original_price' => $data['price'] + 5000,
                    'currency' => 'KES',
                    'is_featured' => true,
                    'is_active' => true,
                    'is_published' => true,
                    'rating' => 4.8,
                    'review_count' => 0,
                    'enrollment_count' => 0,
                    'curriculum' => collect($data['modules'])->map(fn ($m) => ['title' => $m, 'lessons' => []])->all(),
                    'outcomes' => $data['skills'],
                    'career_opportunities' => $data['careers'],
                    // "Who this course is for" — the audience named in the
                    // brief's own Hero subheading, not per-course specific.
                    'requirements' => [
                        'Open to students, graduates, job seekers, entrepreneurs and professionals',
                        'A smartphone, tablet or computer with internet access',
                        'No prior experience required',
                    ],
                    'seo_title' => $data['category'].' Course | KAZI Link Academy',
                    'seo_description' => $data['outcome'],
                    'seo_keywords' => implode(', ', $data['modules']),
                    'order_index' => $data['order'],
                ]
            );

            if ($program->cohorts()->doesntExist()) {
                Cohort::factory()->create([
                    'program_id' => $program->id,
                    'trainer_id' => $trainers->random()->id,
                ]);
            }
        }
    }

    /**
     * @return list<array{category: string, icon: string, color: string, order: int, level: string, price: int, modules: list<string>, outcome: string, skills: list<string>, careers: list<string>}>
     */
    private function courses(): array
    {
        return [
            [
                'category' => 'Artificial Intelligence',
                'icon' => 'zap',
                'color' => '#6366f1',
                'order' => 1,
                'level' => 'intermediate',
                'price' => 19999,
                'modules' => ['ChatGPT Masterclass', 'Prompt Engineering', 'AI for Business', 'AI Content Creation'],
                'outcome' => 'Learn AI to improve productivity, automate work, create content and generate income.',
                'skills' => [
                    'Use ChatGPT professionally', 'Create content', 'Write proposals', 'Write reports',
                    'Build AI workflows', 'Offer AI consulting', 'Become a Prompt Engineer',
                ],
                'careers' => [
                    'AI Prompt Engineer', 'AI Content Creator', 'AI Consultant',
                    'Business Automation Consultant', 'Freelance AI Assistant',
                ],
            ],
            [
                'category' => 'Digital Skills',
                'icon' => 'book-open',
                'color' => '#0ea5e9',
                'order' => 2,
                'level' => 'beginner',
                'price' => 12999,
                'modules' => ['Microsoft Office', 'Google Workspace', 'Internet Research', 'Email Management', 'Digital Literacy'],
                'outcome' => 'Develop essential computer skills for work, study and business.',
                'skills' => [
                    'Use Microsoft Office confidently', 'Navigate Google Workspace tools',
                    'Conduct effective internet research', 'Manage professional email communication',
                    'Apply core digital literacy skills',
                ],
                'careers' => [
                    'Office Administrator', 'Administrative Assistant', 'Receptionist',
                    'Customer Support Officer', 'Data Entry Clerk',
                ],
            ],
            [
                'category' => 'Creative Design',
                'icon' => 'image',
                'color' => '#ec4899',
                'order' => 3,
                'level' => 'beginner',
                'price' => 16999,
                'modules' => ['Canva', 'Photoshop', 'Illustrator', 'CapCut', 'Branding', 'Social Media Design'],
                'outcome' => 'Create professional graphics and videos.',
                'skills' => [
                    'Design graphics in Canva', 'Edit photos in Photoshop', 'Create vector art in Illustrator',
                    'Edit videos in CapCut', 'Build a brand identity', 'Design for social media',
                ],
                'careers' => [
                    'Graphic Designer', 'Brand Designer', 'Video Editor', 'Content Creator', 'Freelance Designer',
                ],
            ],
            [
                'category' => 'Digital Marketing',
                'icon' => 'trending-up',
                'color' => '#f59e0b',
                'order' => 4,
                'level' => 'intermediate',
                'price' => 17999,
                'modules' => ['Facebook Ads', 'Instagram Ads', 'Google Ads', 'SEO', 'Email Marketing', 'Content Marketing'],
                'outcome' => 'Help businesses grow online.',
                'skills' => [
                    'Run Facebook Ads campaigns', 'Run Instagram Ads campaigns', 'Run Google Ads campaigns',
                    'Improve search rankings with SEO', 'Build email marketing campaigns', 'Create content marketing strategies',
                ],
                'careers' => [
                    'Digital Marketing Specialist', 'SEO Specialist', 'Marketing Consultant', 'Social Media Manager',
                ],
            ],
            [
                'category' => 'Freelancing & Online Work',
                'icon' => 'globe',
                'color' => '#10b981',
                'order' => 5,
                'level' => 'beginner',
                'price' => 13999,
                'modules' => ['Academic Writing', 'Virtual Assistance', 'Transcription', 'Data Entry', 'Upwork', 'Fiverr', 'LinkedIn'],
                'outcome' => 'Start earning online.',
                'skills' => [
                    'Write academic papers professionally', 'Provide virtual assistant support', 'Transcribe audio accurately',
                    'Perform accurate data entry', 'Win jobs on Upwork', 'Win jobs on Fiverr', 'Build a LinkedIn profile that attracts clients',
                ],
                'careers' => [
                    'Academic Writer', 'Virtual Assistant', 'Online Researcher', 'Freelancer', 'Remote Worker',
                ],
            ],
            [
                'category' => 'Web & Technology',
                'icon' => 'layers',
                'color' => '#8b5cf6',
                'order' => 6,
                'level' => 'intermediate',
                'price' => 21999,
                'modules' => ['Web Design', 'WordPress', 'HTML', 'CSS', 'JavaScript', 'Website Management'],
                'outcome' => 'Build modern websites.',
                'skills' => [
                    'Design modern websites', 'Build and manage WordPress sites', 'Write HTML markup',
                    'Style pages with CSS', 'Add interactivity with JavaScript', 'Manage and maintain live websites',
                ],
                'careers' => [
                    'Web Designer', 'Front-End Developer', 'WordPress Developer', 'Website Administrator',
                ],
            ],
        ];
    }
}
