<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Disciplinas</h1>
            <p class="mt-1 text-sm text-gray-500">Deportes disponibles dentro de cada torneo.</p>
        </div>
        @can('disciplines.manage')
            <button wire:click="openCreate"
                    class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                </svg>
                Nueva disciplina
            </button>
        @endcan
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filtros --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
        <div class="flex flex-1 items-center gap-3">
            <svg class="h-5 w-5 shrink-0 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search"
                   type="text"
                   placeholder="Buscar disciplina…"
                   class="w-full bg-transparent text-sm text-gray-900 placeholder-gray-400 outline-none">
        </div>
        <select wire:model.live="filterTournament"
                class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 outline-none focus:border-gray-400">
            <option value="">Todos los torneos</option>
            @foreach ($tournaments as $t)
                <option value="{{ $t->id }}">{{ $t->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Tabla --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Disciplina</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Deporte</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Torneo</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($disciplines as $discipline)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $discipline->name }}</td>
                        <td class="px-5 py-4 text-sm text-gray-600">
                            {{ $discipline->config['sport'] ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600">
                            {{ $discipline->tournament?->name ?? '—' }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                @can('disciplines.manage')
                                    <button wire:click="openEdit('{{ $discipline->id }}')"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition-colors"
                                            title="Editar">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 0 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete('{{ $discipline->id }}')"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors"
                                            title="Eliminar">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-sm text-gray-400">
                            No hay disciplinas creadas aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($disciplines->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $disciplines->links() }}
            </div>
        @endif
    </div>

    {{-- Modal crear / editar --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-gray-900/50" wire:click="$set('showModal', false)"></div>
            <div class="relative z-10 w-full max-w-md rounded-2xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-900">
                        {{ $editingId ? 'Editar disciplina' : 'Nueva disciplina' }}
                    </h2>
                    <button wire:click="$set('showModal', false)"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form wire:submit="save" class="space-y-4 px-6 py-5">

                    {{-- Torneo --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">
                            Torneo <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="tournament_id"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 outline-none focus:border-gray-400 transition-colors @error('tournament_id') border-red-400 bg-red-50 @enderror">
                            <option value="">Seleccioná un torneo</option>
                            @foreach ($tournaments as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                        @error('tournament_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nombre --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="name" type="text" placeholder="Football, Pádel, Básquet…"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none focus:border-gray-400 transition-colors @error('name') border-red-400 bg-red-50 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deporte --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Deporte (config)</label>
                        <input wire:model="sport" type="text" placeholder="football, padel, basketball…"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none focus:border-gray-400 transition-colors">
                        <p class="mt-1 text-xs text-gray-400">Opcional. Identificador interno del deporte.</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                            <span wire:loading.remove wire:target="save">{{ $editingId ? 'Guardar' : 'Crear' }}</span>
                            <span wire:loading wire:target="save">Guardando…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal confirmar eliminación --}}
    @if ($confirmingDeleteId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-gray-900/50" wire:click="cancelDelete"></div>
            <div class="relative z-10 w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-5 w-5 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Eliminar disciplina</h3>
                        <p class="mt-1 text-sm text-gray-500">Se eliminarán también todas sus categorías asociadas.</p>
                    </div>
                </div>
                <div class="mt-5 flex justify-end gap-3">
                    <button wire:click="cancelDelete" class="rounded-xl px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button wire:click="delete" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">Eliminar</button>
                </div>
            </div>
        </div>
    @endif

</div>
