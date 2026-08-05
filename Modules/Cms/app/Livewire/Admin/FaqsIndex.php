<?php

namespace Modules\Cms\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Cms\Models\Faq;

#[Layout('admin::components.layouts.admin', ['title' => 'FAQs'])]
class FaqsIndex extends Component
{
    use WithPagination;

    public bool $showForm = false;

    public ?string $editingId = null;

    public array $formData = [];

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    protected function defaultForm(): array
    {
        return [
            'category' => 'general', 'order_index' => '0', 'question' => '', 'answer' => '', 'is_published' => true, 'is_popular' => false,
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
        $faq = Faq::findOrFail($id);
        $this->editingId = $id;
        $this->formData = [
            'category' => $faq->category,
            'order_index' => (string) $faq->order_index,
            'question' => $faq->question,
            'answer' => $faq->answer,
            'is_published' => $faq->is_published,
            'is_popular' => $faq->is_popular,
        ];
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'formData.question' => 'required|string',
            'formData.answer' => 'required|string',
        ], [
            'formData.question.required' => 'Question and answer required',
            'formData.answer.required' => 'Question and answer required',
        ]);

        $payload = [
            'category' => $this->formData['category'] ?: 'general',
            'order_index' => (int) $this->formData['order_index'],
            'question' => $this->formData['question'],
            'answer' => $this->formData['answer'],
            'is_published' => $this->formData['is_published'],
            'is_popular' => $this->formData['is_popular'],
        ];

        if ($this->editingId) {
            Faq::findOrFail($this->editingId)->update($payload);
        } else {
            Faq::create($payload);
        }

        $this->showForm = false;
    }

    public function delete(string $id): void
    {
        Faq::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('cms::livewire.admin.faqs-index', [
            'faqs' => Faq::query()
                ->when($this->search, fn ($q) => $q->where(function ($q) {
                    $q->where('question', 'like', "%{$this->search}%")
                        ->orWhere('answer', 'like', "%{$this->search}%");
                }))
                ->orderBy('order_index')
                ->paginate(25),
        ]);
    }
}
