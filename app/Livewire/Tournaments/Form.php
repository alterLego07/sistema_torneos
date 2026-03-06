<?php

namespace App\Livewire\Tournaments;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Domains\Tournaments\Domain\Models\Tournament;

#[Layout('layouts.dashboard')]
class Form extends Component
{
    public string $name = '';
    public string $season = '';
    public string $starts_on = '';
    public string $ends_on = '';
    public string $status = 'draft';

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

    public function save(): void
    {
        $this->validate();

        Tournament::create([
            'name'      => $this->name,
            'season'    => $this->season ?: null,
            'starts_on' => $this->starts_on ?: null,
            'ends_on'   => $this->ends_on ?: null,
            'status'    => $this->status,
        ]);

        session()->flash('success', 'Torneo creado correctamente.');
        $this->redirect(route('tournaments.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.tournaments.form');
    }
}
