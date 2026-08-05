<?php

namespace Modules\Cms\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Cms\Models\Testimonial;

#[Layout('admin::components.layouts.admin', ['title' => 'Testimonials'])]
class TestimonialsIndex extends Component
{
    use WithPagination;

    public bool $showForm = false;

    public ?string $editingId = null;

    public array $formData = [];

    protected function defaultForm(): array
    {
        return [
            'student_avatar_url' => '', 'student_name' => '', 'student_title' => '',
            'income_before' => '', 'income_after' => '', 'content' => '', 'rating' => '5',
            'is_published' => true, 'is_featured' => false,
        ];
    }

    public function mount(): void
    {
        $this->formData = $this->defaultForm();
    }

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->formData = $this->defaultForm();
        $this->showForm = true;
    }

    public function openEdit(string $id): void
    {
        $testimonial = Testimonial::findOrFail($id);
        $this->editingId = $id;
        $this->formData = [
            'student_avatar_url' => $testimonial->student_avatar_url ?? '',
            'student_name' => $testimonial->student_name,
            'student_title' => $testimonial->student_title ?? '',
            'income_before' => $testimonial->income_before ?? '',
            'income_after' => $testimonial->income_after ?? '',
            'content' => $testimonial->content,
            'rating' => (string) ((int) $testimonial->rating ?: 5),
            'is_published' => $testimonial->is_published,
            'is_featured' => $testimonial->is_featured,
        ];
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'formData.student_name' => 'required|string',
            'formData.content' => 'required|string',
        ], [
            'formData.student_name.required' => 'Name and testimonial content required',
            'formData.content.required' => 'Name and testimonial content required',
        ]);

        $payload = [
            'student_avatar_url' => $this->formData['student_avatar_url'] ?: null,
            'student_name' => $this->formData['student_name'],
            'student_title' => $this->formData['student_title'] ?: null,
            'income_before' => $this->formData['income_before'] ?: null,
            'income_after' => $this->formData['income_after'] ?: null,
            'content' => $this->formData['content'],
            'rating' => (int) $this->formData['rating'],
            'is_published' => $this->formData['is_published'],
            'is_featured' => $this->formData['is_featured'],
        ];

        if ($this->editingId) {
            Testimonial::findOrFail($this->editingId)->update($payload);
        } else {
            $payload['order_index'] = Testimonial::count() + 1;
            Testimonial::create($payload);
        }

        $this->showForm = false;
    }

    public function delete(string $id): void
    {
        Testimonial::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('cms::livewire.admin.testimonials-index', [
            'testimonials' => Testimonial::orderBy('order_index')->paginate(20),
        ]);
    }
}
