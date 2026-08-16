<?php

namespace Tests\Feature;

use Modules\Cms\Database\Seeders\CmsDatabaseSeeder;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    public function test_terms_privacy_refund_and_ol_kalou_offer_pages_are_published(): void
    {
        (new CmsDatabaseSeeder)->seedLegalPages();

        $this->get('/terms')->assertOk()->assertSee('Terms & Conditions')->assertSee('User Registration and Accounts');
        $this->get('/privacy')->assertOk()->assertSee('Privacy Policy')->assertSee('Your Data Protection Rights');
        $this->get('/refund')->assertOk()->assertSee('Refund Policy')->assertSee('How to Request a Refund');
        $this->get('/ol-kalou-offer')->assertOk()->assertSee('Ol Kalou Special Offer Notice')->assertSee('Who Qualifies?');
    }

    public function test_re_seeding_legal_pages_does_not_duplicate_sections(): void
    {
        $seeder = new CmsDatabaseSeeder;
        $seeder->seedLegalPages();
        $seeder->seedLegalPages();

        $page = \Modules\Cms\Models\Page::where('slug', 'terms')->firstOrFail();

        $this->assertSame(41, $page->blocks()->count());
    }
}
