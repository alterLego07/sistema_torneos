<?php

namespace App\Livewire\Tournaments;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Domains\Tournaments\Domain\Models\Tournament;

#[Layout('layouts.dashboard')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    // Modal edición
    public bool $showModal = false;
    public ?string $editingId = null;
    public string $name = '';
    public string $season = '';
    public string $starts_on = '';
    public string $ends_on = '';
    public string $status = 'draft';

    // Confirmación de eliminación
    public ?string $confirmingDeleteId = null;

    protected array $rules = [
        'name'      => 'required|string|max:255',
        'season'    => 'nullable|string|max:100',
        'starts_on' => 'nullable|date',
        'ends_on'   => 'nullable|date|after_or_equal:starts_on',
        'status'    => 'required|in:draft,published,running,finished',
    ];

    protected array $messages = [
        'name.required'          => 'El nombre es obligatorio.',
        'ends_on.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la de inicio.',
        'status.in'              => 'Estado inválido.',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(string $id): void
    {
        $tournament = Tournament::findOrFail($id);
        $this->editingId  = $tournament->id;
        $this->name       = $tournament->name;
        $this->season     = $tournament->season ?? '';
        $this->starts_on  = $tournament->starts_on?->format('Y-m-d') ?? '';
        $this->ends_on    = $tournament->ends_on?->format('Y-m-d') ?? '';
        $this->status     = $tournament->status;
        $this->showModal  = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'      => $this->name,
            'season'    => $this->season ?: null,
            'starts_on' => $this->starts_on ?: null,
            'ends_on'   => $this->ends_on ?: null,
            'status'    => $this->status,
        ];

        if ($this->editingId) {
            Tournament::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Torneo actualizado correctamente.');
        } else {
            Tournament::create($data);
            session()->flash('success', 'Torneo creado correctamente.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(string $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(): void
    {
        if ($this->confirmingDeleteId) {
            Tournament::findOrFail($this->confirmingDeleteId)->delete();
            session()->flash('success', 'Torneo eliminado.');
            $this->confirmingDeleteId = null;
        }
    }

    private function resetForm(): void
    {
        $this->name      = '';
        $this->season    = '';
        $this->starts_on = '';
        $this->ends_on   = '';
        $this->status    = 'draft';
        $this->resetValidation();
    }

    public function render()
    {
        $tournaments = Tournament::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('season', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.tournaments.index', compact('tournaments'));
    }
}
