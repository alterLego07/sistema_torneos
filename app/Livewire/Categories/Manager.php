<?php

namespace App\Livewire\Categories;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Domains\Tournaments\Domain\Models\Discipline;
use App\Domains\Tournaments\Domain\Models\Category;

#[Layout('layouts.dashboard')]
class Manager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterDiscipline = '';

    // Modal
    public bool $showModal = false;
    public ?string $editingId = null;
    public string $discipline_id = '';
    public string $name = '';
    public string $format = 'league';
    public string $team_size = '2';
    public string $min_players = '';
    public string $max_players = '';

    // Confirmación de eliminación
    public ?string $confirmingDeleteId = null;

    protected array $rules = [
        'discipline_id' => 'required|string',
        'name'          => 'required|string|max:255',
        'format'        => 'required|in:league,knockout',
        'team_size'     => 'required|integer|min:1|max:50',
        'min_players'   => 'nullable|integer|min:1',
        'max_players'   => 'nullable|integer|min:1|gte:min_players',
    ];

    protected array $messages = [
        'discipline_id.required' => 'Seleccioná una disciplina.',
        'name.required'          => 'El nombre es obligatorio.',
        'format.in'              => 'Formato inválido.',
        'team_size.required'     => 'El tamaño de equipo es obligatorio.',
        'max_players.gte'        => 'El máximo debe ser mayor o igual al mínimo.',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDiscipline(): void
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
        $category = Category::findOrFail($id);
        $this->editingId     = $category->id;
        $this->discipline_id = $category->discipline_id;
        $this->name          = $category->name;
        $this->format        = $category->format;
        $this->team_size     = (string) $category->team_size;
        $this->min_players   = $category->min_players ? (string) $category->min_players : '';
        $this->max_players   = $category->max_players ? (string) $category->max_players : '';
        $this->showModal     = true;
    }

    public function save(): void
    {
        $this->validate();

        $rules = $this->format === 'league'
            ? ['points_win' => 3, 'points_draw' => 1, 'points_loss' => 0, 'tiebreakers' => ['points', 'goal_diff', 'goals_for']]
            : ['best_of_sets' => 3, 'tiebreak' => true];

        $data = [
            'discipline_id' => $this->discipline_id,
            'name'          => $this->name,
            'format'        => $this->format,
            'team_size'     => (int) $this->team_size,
            'min_players'   => $this->min_players !== '' ? (int) $this->min_players : null,
            'max_players'   => $this->max_players !== '' ? (int) $this->max_players : null,
        ];

        if ($this->editingId) {
            Category::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Categoría actualizada correctamente.');
        } else {
            Category::create(array_merge($data, ['rules' => $rules]));
            session()->flash('success', 'Categoría creada correctamente.');
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
            Category::findOrFail($this->confirmingDeleteId)->delete();
            session()->flash('success', 'Categoría eliminada.');
            $this->confirmingDeleteId = null;
        }
    }

    private function resetForm(): void
    {
        $this->discipline_id = '';
        $this->name          = '';
        $this->format        = 'league';
        $this->team_size     = '2';
        $this->min_players   = '';
        $this->max_players   = '';
        $this->resetValidation();
    }

    public function render()
    {
        $disciplines = Discipline::with('tournament:id,name')
            ->orderBy('name')
            ->get();

        $categories = Category::query()
            ->with('discipline:id,name')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->filterDiscipline, fn($q) => $q->where('discipline_id', $this->filterDiscipline))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.categories.manager', compact('categories', 'disciplines'));
    }
}
