<?php

namespace Tests\Feature;

use Livewire\Livewire;
use Modules\Academy\Models\Program;
use Modules\Booking\Livewire\BookingWizard;
use Tests\TestCase;

class BookingWizardPaymentUiTest extends TestCase
{
    public function test_payment_step_renders_mpesa_stk_push_and_bank(): void
    {
        $program = Program::factory()->create([
            'title' => 'AI Career Accelerator',
            'price' => 12999,
            'currency' => 'KES',
            'is_active' => true,
            'is_published' => true,
        ]);

        Livewire::test(BookingWizard::class)
            ->set('selectedProgram', $program->toArray())
            ->set('step', 3)
            ->set('paymentMethod', 'mpesa')
            ->assertSee('Pay with M-Pesa')
            ->assertSee('Safaricom Phone Number')
            ->assertSee('Pay Now')
            ->assertSee('Bank Transfer')
            ->assertDontSee('Visa / Mastercard')
            ->assertDontSee('Review Booking');
    }
}
