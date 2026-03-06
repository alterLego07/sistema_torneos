<?php

namespace App\Livewire\Players;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Domains\Registration\Domain\Models\Player;

#[Layout('layouts.dashboard')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterGender = '';

    // Modal
    public bool $showModal = false;
    public ?string $editingId = null;
    public string $first_name = '';
    public string $last_name = '';
    public string $document = '';
    public string $birthdate = '';
    public string $gender = '';
    public string $phone = '';
    public string $email = '';

    // Confirmación de eliminación
    public ?string $confirmingDeleteId = null;

    protected array $rules = [
        'first_name' => 'required|string|max:100',
        'last_name'  => 'required|string|max:100',
        'document'   => 'nullable|string|max:50',
        'birthdate'  => 'nullable|date',
        'gender'     => 'nullable|in:M,F,X',
        'phone'      => 'nullable|string|max:30',
        'email'      => 'nullable|email|max:150',
    ];

    protected array $messages = [
        'first_name.required' => 'El nombre es obligatorio.',
        'last_name.required'  => 'El apellido es obligatorio.',
        'email.email'         => 'El email no tiene un formato válido.',
        'gender.in'           => 'Género inválido.',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterGender(): void
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
        $player = Player::findOrFail($id);
        $this->editingId  = $player->id;
        $this->first_name = $player->first_name;
        $this->last_name  = $player->last_name;
        $this->document   = $player->document ?? '';
        $this->birthdate  = $player->birthdate?->format('Y-m-d') ?? '';
        $this->gender     = $player->gender ?? '';
        $this->phone      = $player->phone ?? '';
        $this->email      = $player->email ?? '';
        $this->showModal  = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'document'   => $this->document ?: null,
            'birthdate'  => $this->birthdate ?: null,
            'gender'     => $this->gender ?: null,
            'phone'      => $this->phone ?: null,
            'email'      => $this->email ?: null,
        ];

        if ($this->editingId) {
            Player::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Jugador actualizado correctamente.');
        } else {
            Player::create($data);
            session()->flash('success', 'Jugador creado correctamente.');
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
            Player::findOrFail($this->confirmingDeleteId)->delete();
            session()->flash('success', 'Jugador eliminado.');
            $this->confirmingDeleteId = null;
        }
    }

    private function resetForm(): void
    {
        $this->first_name = '';
        $this->last_name  = '';
        $this->document   = '';
        $this->birthdate  = '';
        $this->gender     = '';
        $this->phone      = '';
        $this->email      = '';
        $this->resetValidation();
    }

    public function render()
    {
        $players = Player::query()
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
                  ->orWhere('document', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->filterGender, fn($q) => $q->where('gender', $this->filterGender))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15);

        return view('livewire.players.index', compact('players'));
    }
}
