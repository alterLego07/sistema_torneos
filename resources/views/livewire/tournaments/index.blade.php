<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Torneos</h1>
            <p class="mt-1 text-sm text-gray-500">Gestión de todos los torneos del sistema.</p>
        </div>

        @can('tournaments.create')
            <button wire:click="openCreate"
                    class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                </svg>
                Nuevo torneo
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

    {{-- Search --}}
    <div class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
        <svg class="h-5 w-5 shrink-0 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
        </svg>
        <input wire:model.live.debounce.300ms="search"
               type="text"
               placeholder="Buscar por nombre o temporada…"
               class="w-full bg-transparent text-sm text-gray-900 placeholder-gray-400 outline-none">
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nombre</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Temporada</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Fechas</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($tournaments as $tournament)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <span class="font-medium text-gray-900">{{ $tournament->name }}</span>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600">
                            {{ $tournament->season ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600">
                            @if ($tournament->starts_on || $tournament->ends_on)
                                <span>{{ $tournament->starts_on?->format('d/m/Y') ?? '?' }}</span>
                                <span class="mx-1 text-gray-300">→</span>
                                <span>{{ $tournament->ends_on?->format('d/m/Y') ?? '?' }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $badge = match($tournament->status) {
                                    'draft'     => 'bg-gray-100 text-gray-600',
                                    'published' => 'bg-blue-50 text-blue-700',
                                    'running'   => 'bg-green-50 text-green-700',
                                    'finished'  => 'bg-orange-50 text-orange-700',
                                    default     => 'bg-gray-100 text-gray-600',
                                };
                                $label = match($tournament->status) {
                                    'draft'     => 'Borrador',
                                    'published' => 'Publicado',
                                    'running'   => 'En curso',
                                    'finished'  => 'Finalizado',
                                    default     => $tournament->status,
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badge }}">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                @can('tournaments.update')
                                    <button wire:click="openEdit('{{ $tournament->id }}')"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition-colors"
                                            title="Editar">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 0 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                @endcan

                                @can('tournaments.delete')
                                    <button wire:click="confirmDelete('{{ $tournament->id }}')"
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
                        <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-400">
                            @if ($search)
                                No se encontraron torneos para "<strong>{{ $search }}</strong>".
                            @else
                                No hay torneos creados aún.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($tournaments->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $tournaments->links() }}
            </div>
        @endif
    </div>

    {{-- Modal crear / editar --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            {{-- Overlay --}}
            <div class="absolute inset-0 bg-gray-900/50" wire:click="$set('showModal', false)"></div>

            {{-- Panel --}}
            <div class="relative z-10 w-full max-w-lg rounded-2xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-900">
                        {{ $editingId ? 'Editar torneo' : 'Nuevo torneo' }}
                    </h2>
                    <button wire:click="$set('showModal', false)"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="space-y-5 px-6 py-5">

                    {{-- Nombre --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="name" type="text" placeholder="Copa Chacomer 2026"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none focus:border-gray-400 transition-colors @error('name') border-red-400 bg-red-50 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Temporada --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Temporada</label>
                        <input wire:model="season" type="text" placeholder="2026 / Apertura 2026"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none focus:border-gray-400 transition-colors">
                    </div>

                    {{-- Fechas --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Fecha inicio</label>
                            <input wire:model="starts_on" type="date"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 outline-none focus:border-gray-400 transition-colors @error('starts_on') border-red-400 bg-red-50 @enderror">
                            @error('starts_on')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Fecha fin</label>
                            <input wire:model="ends_on" type="date"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 outline-none focus:border-gray-400 transition-colors @error('ends_on') border-red-400 bg-red-50 @enderror">
                            @error('ends_on')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="status"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 outline-none focus:border-gray-400 transition-colors @error('status') border-red-400 bg-red-50 @enderror">
                            <option value="draft">Borrador</option>
                            <option value="published">Publicado</option>
                            <option value="running">En curso</option>
                            <option value="finished">Finalizado</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Acciones --}}
                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                            <span wire:loading.remove wire:target="save">
                                {{ $editingId ? 'Guardar cambios' : 'Crear torneo' }}
                            </span>
                            <span wire:loading wire:target="save">Guardando…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal confirmación de eliminación --}}
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
                        <h3 class="text-sm font-semibold text-gray-900">Eliminar torneo</h3>
                        <p class="mt-1 text-sm text-gray-500">Esta acción es irreversible. Se eliminarán también las disciplinas y categorías asociadas.</p>
                    </div>
                </div>
                <div class="mt-5 flex justify-end gap-3">
                    <button wire:click="cancelDelete"
                            class="rounded-xl px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="delete"
                            class="rounded-xl bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
