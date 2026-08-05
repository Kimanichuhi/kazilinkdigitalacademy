<?php

namespace Modules\Cms\Livewire;

use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\Cms\Contracts\SiteSettingLookupContract;
use Modules\Cms\Models\ContactSubmission;

#[Layout('core::components.layouts.public', ['title' => 'Contact Us'])]
class ContactPage extends Component
{
    public string $full_name = '';

    public string $email = '';

    public string $phone = '';

    public string $subject = 'enrollment';

    public string $message = '';

    public bool $submitted = false;

    public function submit(): void
    {
        $throttleKey = 'contact-form:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $this->addError('message', 'Too many submissions. Please try again in a few minutes.');

            return;
        }

        RateLimiter::hit($throttleKey, 600);

        $this->validate([
            'full_name' => 'required|string|min:2',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'subject' => 'required|in:enrollment,payment,technical,corporate,other',
            'message' => 'required|string|min:10',
        ]);

        ContactSubmission::create([
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => 'new',
        ]);

        $this->reset(['full_name', 'email', 'phone', 'message']);
        $this->subject = 'enrollment';
        $this->submitted = true;
    }

    public function render(SiteSettingLookupContract $settings)
    {
        return view('cms::livewire.contact-page', [
            'settings' => $settings->getMany(['contact_email', 'contact_phone', 'contact_whatsapp', 'contact_address']),
        ]);
    }
}
