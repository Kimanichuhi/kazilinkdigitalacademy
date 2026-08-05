<?php

namespace Modules\Cms\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\Cms\Models\Page;
use Modules\Cms\Models\PageBlock;

/**
 * Generic content editor for the flexible Page + PageBlock model, used to
 * make narrative/marketing-copy pages (About, Pricing, Privacy, Terms,
 * Refund) admin-editable without a bespoke CRUD screen per page. Each block
 * is a `{type, heading, subtitle, body, meta}` record; `type` groups blocks
 * for the public templates (e.g. `timeline_item`, `addon`) and `meta` is a
 * small free-form JSON object for anything that doesn't fit heading/body
 * (icon name, year, price).
 */
#[Layout('admin::components.layouts.admin', ['title' => 'Pages'])]
class PagesIndex extends Component
{
    public ?string $editingSlug = null;

    public array $pageMeta = ['title' => '', 'is_published' => true];

    /** @var array<int, array{id: ?string, type: string, heading: string, subtitle: string, body: string, meta_json: string, order_index: int, is_active: bool}> */
    public array $blocks = [];

    public function openEdit(string $slug): void
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        $this->editingSlug = $slug;
        $this->pageMeta = ['title' => $page->title, 'is_published' => $page->is_published];
        $this->blocks = $page->blocks()->get()->map(fn (PageBlock $block) => [
            'id' => $block->id,
            'type' => $block->type,
            'heading' => $block->content['heading'] ?? '',
            'subtitle' => $block->content['subtitle'] ?? '',
            'body' => $block->content['body'] ?? '',
            'meta_json' => $block->content['meta'] ? json_encode($block->content['meta'], JSON_PRETTY_PRINT) : '',
            'order_index' => $block->order_index,
            'is_active' => $block->is_active,
        ])->all();
    }

    public function close(): void
    {
        $this->editingSlug = null;
        $this->blocks = [];
    }

    public function addBlock(): void
    {
        $this->blocks[] = [
            'id' => null,
            'type' => $this->blocks[0]['type'] ?? 'section',
            'heading' => '',
            'subtitle' => '',
            'body' => '',
            'meta_json' => '',
            'order_index' => count($this->blocks),
            'is_active' => true,
        ];
    }

    public function removeBlock(int $index): void
    {
        if (isset($this->blocks[$index]['id'])) {
            PageBlock::whereKey($this->blocks[$index]['id'])->delete();
        }

        unset($this->blocks[$index]);
        $this->blocks = array_values($this->blocks);
    }

    public function save(): void
    {
        $this->validate([
            'pageMeta.title' => 'required|string',
            'blocks.*.type' => 'required|string',
        ]);

        $page = Page::where('slug', $this->editingSlug)->firstOrFail();
        $page->update([
            'title' => $this->pageMeta['title'],
            'is_published' => $this->pageMeta['is_published'],
        ]);

        foreach ($this->blocks as $index => $block) {
            $meta = null;

            if (trim($block['meta_json']) !== '') {
                $decoded = json_decode($block['meta_json'], true);
                $meta = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
            }

            $content = [
                'heading' => $block['heading'] ?: null,
                'subtitle' => $block['subtitle'] ?: null,
                'body' => $block['body'] ?: null,
                'meta' => $meta,
            ];

            $payload = [
                'page_id' => $page->id,
                'type' => $block['type'],
                'content' => $content,
                'order_index' => $index,
                'is_active' => $block['is_active'],
            ];

            if ($block['id']) {
                PageBlock::whereKey($block['id'])->update($payload);
            } else {
                $this->blocks[$index]['id'] = PageBlock::create($payload)->id;
            }
        }

        session()->flash('pages-saved', 'Page content saved.');
    }

    public function render()
    {
        return view('cms::livewire.admin.pages-index', [
            'pages' => Page::withCount('blocks')->orderBy('title')->get(),
            'blockTypes' => $this->editingSlug ? collect($this->blocks)->pluck('type')->unique()->values() : collect(),
        ]);
    }
}
