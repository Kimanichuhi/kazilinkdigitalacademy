<?php

namespace Tests\Feature;

use Livewire\Livewire;
use Modules\Academy\Models\Program;
use Modules\Booking\Livewire\BookingWizard;
use Tests\TestCase;

class BookingWizardPaymentUiTest extends TestCase
{
    public function test_payment_step_renders_manual_mpesa_and_stk_coming_soon(): void
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
            ->assertSee('M-Pesa Code')
            ->assertSee('M-Pesa Confirmation Code')
            ->assertSee('STK Push')
            ->assertSee('Coming soon')
            ->assertSee('Bank Transfer')
            ->assertDontSee('Visa / Mastercard');
    }
}
