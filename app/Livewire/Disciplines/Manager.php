<?php

namespace App\Livewire\Disciplines;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Domains\Tournaments\Domain\Models\Tournament;
use App\Domains\Tournaments\Domain\Models\Discipline;

#[Layout('layouts.dashboard')]
class Manager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterTournament = '';

    // Modal
    public bool $showModal = false;
    public ?string $editingId = null;
    public string $tournament_id = '';
    public string $name = '';
    public string $sport = '';

    // Confirmación de eliminación
    public ?string $confirmingDeleteId = null;

    protected array $rules = [
        'tournament_id' => 'required|string',
        'name'          => 'required|string|max:255',
        'sport'         => 'nullable|string|max:100',
    ];

    protected array $messages = [
        'tournament_id.required' => 'Seleccioná un torneo.',
        'name.required'          => 'El nombre es obligatorio.',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTournament(): void
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
        $discipline = Discipline::findOrFail($id);
        $this->editingId     = $discipline->id;
        $this->tournament_id = $discipline->tournament_id;
        $this->name          = $discipline->name;
        $this->sport         = $discipline->config['sport'] ?? '';
        $this->showModal     = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'tournament_id' => $this->tournament_id,
            'name'          => $this->name,
            'config'        => $this->sport ? ['sport' => $this->sport] : null,
        ];

        if ($this->editingId) {
            Discipline::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Disciplina actualizada correctamente.');
        } else {
            Discipline::create($data);
            session()->flash('success', 'Disciplina creada correctamente.');
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
            Discipline::findOrFail($this->confirmingDeleteId)->delete();
            session()->flash('success', 'Disciplina eliminada.');
            $this->confirmingDeleteId = null;
        }
    }

    private function resetForm(): void
    {
        $this->tournament_id = '';
        $this->name          = '';
        $this->sport         = '';
        $this->resetValidation();
    }

    public function render()
    {
        $tournaments = Tournament::orderBy('name')->get();

        $disciplines = Discipline::query()
            ->with('tournament:id,name')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->filterTournament, fn($q) => $q->where('tournament_id', $this->filterTournament))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.disciplines.manager', compact('disciplines', 'tournaments'));
    }
}
