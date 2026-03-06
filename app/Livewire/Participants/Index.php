<?php

namespace App\Livewire\Participants;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Domains\Registration\Domain\Models\Participant;
use App\Domains\Registration\Domain\Models\Player;

#[Layout('layouts.dashboard')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterType = '';

    // Modal principal
    public bool $showModal = false;
    public ?string $editingId = null;
    public string $name = '';
    public string $type = 'team';
    public string $sport = '';
    public string $gender = '';

    // Plantel dentro del modal
    public string $playerSearch = '';
    public array $selectedPlayers = [];

    // Confirmación de eliminación
    public ?string $confirmingDeleteId = null;

    protected array $rules = [
        'name'   => 'required|string|max:255',
        'type'   => 'required|in:team,pair,single',
        'sport'  => 'nullable|string|max:100',
        'gender' => 'nullable|in:M,F,X',
    ];

    protected array $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'type.in'       => 'Tipo inválido.',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterType(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function playerResults()
    {
        if (strlen(trim($this->playerSearch)) < 2) {
            return collect();
        }

        $already = array_column($this->selectedPlayers, 'id');

        return Player::query()
            ->where(fn($q) => $q
                ->where('first_name', 'like', "%{$this->playerSearch}%")
                ->orWhere('last_name', 'like', "%{$this->playerSearch}%")
                ->orWhere('document', 'like', "%{$this->playerSearch}%")
            )
            ->when($already, fn($q) => $q->whereNotIn('id', $already))
            ->orderBy('last_name')
            ->limit(8)
            ->get();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(string $id): void
    {
        $participant = Participant::with('players')->findOrFail($id);
        $this->editingId       = $participant->id;
        $this->name            = $participant->name;
        $this->type            = $participant->type;
        $this->sport           = $participant->metadata['sport'] ?? '';
        $this->gender          = $participant->metadata['gender'] ?? '';
        $this->selectedPlayers = $participant->players->map(fn($p) => [
            'id'   => $p->id,
            'name' => $p->last_name . ', ' . $p->first_name,
            'role' => $p->pivot->role ?? 'player',
        ])->toArray();
        $this->showModal = true;
    }

    public function addPlayer(string $playerId): void
    {
        if (collect($this->selectedPlayers)->contains('id', $playerId)) {
            return;
        }

        $player = Player::find($playerId);
        if (!$player) {
            return;
        }

        $this->selectedPlayers[] = [
            'id'   => $player->id,
            'name' => $player->last_name . ', ' . $player->first_name,
            'role' => 'player',
        ];
        $this->playerSearch = '';
        unset($this->playerResults);
    }

    public function removePlayer(string $playerId): void
    {
        $this->selectedPlayers = collect($this->selectedPlayers)
            ->filter(fn($p) => $p['id'] !== $playerId)
            ->values()
            ->toArray();
    }

    public function updatePlayerRole(string $playerId, string $role): void
    {
        $this->selectedPlayers = collect($this->selectedPlayers)
            ->map(fn($p) => $p['id'] === $playerId ? array_merge($p, ['role' => $role]) : $p)
            ->toArray();
    }

    public function save(): void
    {
        $this->validate();

        $metadata = array_filter([
            'sport'  => $this->sport ?: null,
            'gender' => $this->gender ?: null,
        ]);

        $data = [
            'name'     => $this->name,
            'type'     => $this->type,
            'metadata' => $metadata ?: null,
        ];

        if ($this->editingId) {
            $participant = Participant::findOrFail($this->editingId);
            $participant->update($data);
        } else {
            $participant = Participant::create($data);
        }

        $syncData = collect($this->selectedPlayers)
            ->mapWithKeys(fn($p) => [$p['id'] => ['role' => $p['role']]])
            ->toArray();
        $participant->players()->sync($syncData);

        session()->flash('success', $this->editingId ? 'Participante actualizado.' : 'Participante creado.');
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
            Participant::findOrFail($this->confirmingDeleteId)->delete();
            session()->flash('success', 'Participante eliminado.');
            $this->confirmingDeleteId = null;
        }
    }

    private function resetForm(): void
    {
        $this->name            = '';
        $this->type            = 'team';
        $this->sport           = '';
        $this->gender          = '';
        $this->playerSearch    = '';
        $this->selectedPlayers = [];
        $this->resetValidation();
    }

    public function render()
    {
        $participants = Participant::query()
            ->withCount('players')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.participants.index', compact('participants'));
    }
}
