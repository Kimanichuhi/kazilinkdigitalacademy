<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use App\Models\User;
use Modules\Academy\Models\Program;
use Modules\Booking\Livewire\BookingWizard;
use Modules\Booking\Models\Booking;
use Tests\TestCase;

class BookingWizardOlKalouOfferTest extends TestCase
{
    public function test_ol_kalou_constituency_shows_the_optional_id_photo_prompt(): void
    {
        $program = Program::factory()->create(['is_active' => true, 'is_published' => true]);

        Livewire::test(BookingWizard::class)
            ->set('selectedProgram', $program->toArray())
            ->set('step', 2)
            ->set('county', 'Nyandarua')
            ->set('constituency', 'Ol Kalou')
            ->assertSee('Ol Kalou Special Offer')
            ->assertSee('optional');
    }

    public function test_other_constituencies_do_not_show_the_prompt(): void
    {
        $program = Program::factory()->create(['is_active' => true, 'is_published' => true]);

        Livewire::test(BookingWizard::class)
            ->set('selectedProgram', $program->toArray())
            ->set('step', 2)
            ->set('county', 'Nairobi')
            ->set('constituency', 'Westlands')
            ->assertDontSee('Ol Kalou Special Offer');
    }

    public function test_uploading_an_id_photo_stores_it_on_the_booking(): void
    {
        Storage::fake('local');

        $program = Program::factory()->create(['is_active' => true, 'is_published' => true]);

        $component = Livewire::test(BookingWizard::class)
            ->set('selectedProgram', $program->toArray())
            ->set('step', 2)
            ->set('full_name', 'Jane Wanjiku')
            ->set('email', 'jane@example.com')
            ->set('phone', '0712345678')
            ->set('county', 'Nyandarua')
            ->set('constituency', 'Ol Kalou')
            ->set('olKalouIdConsent', true)
            ->set('idDocumentPhoto', UploadedFile::fake()->image('id.jpg'))
            ->call('continueFromDetails');

        $component->assertHasNoErrors();

        $path = $component->get('idDocumentPath');
        $this->assertNotNull($path);
        $this->assertStringStartsWith('booking-id-documents/', $path);
        Storage::disk('local')->assertExists($path);

        $booking = Booking::where('email', 'jane@example.com')->latest('created_at')->first();
        $this->assertNotNull($booking);
        $this->assertSame($path, $booking->documents_urls['ol_kalou_id_document_path'] ?? null);
        $this->assertArrayNotHasKey('ol_kalou_id_document', $booking->documents_urls ?? []);
    }

    public function test_id_photo_requires_separate_ol_kalou_consent(): void
    {
        Storage::fake('local');

        $program = Program::factory()->create(['is_active' => true, 'is_published' => true]);

        Livewire::test(BookingWizard::class)
            ->set('selectedProgram', $program->toArray())
            ->set('step', 2)
            ->set('full_name', 'Jane Wanjiku')
            ->set('email', 'jane@example.com')
            ->set('phone', '0712345678')
            ->set('county', 'Nyandarua')
            ->set('constituency', 'Ol Kalou')
            ->set('idDocumentPhoto', UploadedFile::fake()->image('id.jpg'))
            ->call('continueFromDetails')
            ->assertHasErrors(['olKalouIdConsent']);
    }

    public function test_id_photo_is_optional_and_does_not_block_booking(): void
    {
        $program = Program::factory()->create(['is_active' => true, 'is_published' => true]);

        Livewire::test(BookingWizard::class)
            ->set('selectedProgram', $program->toArray())
            ->set('step', 2)
            ->set('full_name', 'Jane Wanjiku')
            ->set('email', 'jane@example.com')
            ->set('phone', '0712345678')
            ->set('county', 'Nyandarua')
            ->set('constituency', 'Ol Kalou')
            ->call('continueFromDetails')
            ->assertHasNoErrors()
            ->assertSet('step', 3);
    }

    public function test_stored_id_document_is_only_served_to_the_booking_owner_or_authorized_staff(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('booking-id-documents/id.jpg', 'private-id-file');

        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $owner->id,
            'documents_urls' => ['ol_kalou_id_document_path' => 'booking-id-documents/id.jpg'],
        ]);

        $this->get(route('booking.documents.show', [$booking, 'ol_kalou_id_document']))
            ->assertRedirect('/login');

        $this->actingAs($otherUser)
            ->get(route('booking.documents.show', [$booking, 'ol_kalou_id_document']))
            ->assertForbidden();

        $response = $this->actingAs($owner)
            ->get(route('booking.documents.show', [$booking, 'ol_kalou_id_document']))
            ->assertOk();

        $this->assertSame('private-id-file', $response->streamedContent());
    }
}
