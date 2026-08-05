<?php

namespace Modules\Academy\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Academy\Models\Cohort;
use Modules\Academy\Models\Program;
use Modules\Academy\Models\Trainer;

#[Layout('admin::components.layouts.admin', ['title' => 'Cohorts'])]
class CohortsIndex extends Component
{
    use WithPagination;

    public bool $showForm = false;

    public ?string $editingId = null;

    public array $formData = [];

    protected function defaultForm(): array
    {
        return [
            'program_id' => '', 'trainer_id' => '', 'name' => '', 'start_date' => '', 'end_date' => '',
            'registration_deadline' => '', 'total_seats' => '20', 'schedule_details' => '',
            'venue' => '', 'online_link' => '', 'online_platform' => '', 'price' => '', 'status' => 'upcoming',
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
        $cohort = Cohort::findOrFail($id);
        $this->editingId = $id;
        $this->formData = [
            'program_id' => $cohort->program_id, 'trainer_id' => $cohort->trainer_id ?? '',
            'name' => $cohort->name, 'start_date' => (string) $cohort->start_date?->format('Y-m-d'),
            'end_date' => (string) $cohort->end_date?->format('Y-m-d'),
            'registration_deadline' => (string) $cohort->registration_deadline?->format('Y-m-d'),
            'total_seats' => (string) $cohort->total_seats, 'schedule_details' => $cohort->schedule_details ?? '',
            'venue' => $cohort->venue ?? '', 'online_link' => $cohort->online_link ?? '',
            'online_platform' => $cohort->online_platform ?? '', 'price' => (string) ($cohort->price ?? ''),
            'status' => $cohort->status,
        ];
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'formData.program_id' => 'required',
            'formData.name' => 'required|string',
            'formData.start_date' => 'required|date',
        ], [
            'formData.program_id.required' => 'Program, name, and start date required',
            'formData.name.required' => 'Program, name, and start date required',
            'formData.start_date.required' => 'Program, name, and start date required',
        ]);

        $payload = $this->formData;
        $payload['total_seats'] = (int) ($payload['total_seats'] ?: 20);
        $payload['price'] = $payload['price'] !== '' ? (float) $payload['price'] : null;
        $payload['trainer_id'] = $payload['trainer_id'] ?: null;
        $payload['end_date'] = $payload['end_date'] ?: null;
        $payload['registration_deadline'] = $payload['registration_deadline'] ?: null;
        $payload['currency'] = 'KES';

        if ($this->editingId) {
            Cohort::findOrFail($this->editingId)->update($payload);
        } else {
            Cohort::create($payload);
        }

        $this->showForm = false;
    }

    public function delete(string $id): void
    {
        Cohort::findOrFail($id)->delete();
    }

    public function render()
    {
        $cohorts = Cohort::with(['program', 'trainer'])->orderByDesc('start_date')->paginate(20);
        $programs = Program::orderBy('title')->get(['id', 'title', 'is_published', 'is_active']);
        $trainers = Trainer::orderBy('full_name')->get(['id', 'full_name', 'is_active']);

        return view('academy::livewire.admin.cohorts-index', [
            'cohorts' => $cohorts,
            'programs' => $programs,
            'trainers' => $trainers,
            'availablePrograms' => $programs->where('is_published', true)->where('is_active', true),
            'activeTrainers' => $trainers->where('is_active', true),
        ]);
    }
}
