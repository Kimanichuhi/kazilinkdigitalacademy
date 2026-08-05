<?php

namespace Modules\Cms\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\Cms\Models\Faq;

#[Layout('core::components.layouts.public', ['title' => 'FAQs'])]
class FaqPage extends Component
{
    public string $search = '';

    public string $category = '';

    public function render()
    {
        $faqs = Faq::query()
            ->where('is_published', true)
            ->when($this->search, fn ($query) => $query->where(function ($q) {
                $q->where('question', 'like', "%{$this->search}%")
                    ->orWhere('answer', 'like', "%{$this->search}%");
            }))
            ->when($this->category, fn ($query) => $query->where('category', $this->category))
            ->orderBy('order_index')
            ->get();

        $popular = Faq::query()
            ->where('is_published', true)
            ->where('is_popular', true)
            ->orderBy('order_index')
            ->get();

        return view('cms::livewire.faq-page', [
            'grouped' => $faqs->groupBy('category'),
            'categories' => Faq::where('is_published', true)->distinct()->pluck('category'),
            'popular' => $popular,
        ]);
    }
}
