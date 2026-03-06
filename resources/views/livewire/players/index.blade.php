<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Jugadores</h1>
            <p class="mt-1 text-sm text-gray-500">Registro de todos los jugadores del sistema.</p>
        </div>
        @can('players.manage')
            <button wire:click="openCreate"
                    class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                </svg>
                Nuevo jugador
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
                   placeholder="Buscar por nombre, apellido, documento o email…"
                   class="w-full bg-transparent text-sm text-gray-900 placeholder-gray-400 outline-none">
        </div>
        <select wire:model.live="filterGender"
                class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 outline-none focus:border-gray-400">
            <option value="">Todos los géneros</option>
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
            <option value="X">Otro</option>
        </select>
    </div>

    {{-- Tabla --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jugador</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Documento</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Género</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nacimiento</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Contacto</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($players as $player)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold text-gray-600">
                                    {{ strtoupper(substr($player->first_name, 0, 1) . substr($player->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $player->last_name }}, {{ $player->first_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600">{{ $player->document ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @php
                                $gBadge = match($player->gender) {
                                    'M' => ['bg-blue-50 text-blue-700', 'Masc.'],
                                    'F' => ['bg-pink-50 text-pink-700', 'Fem.'],
                                    'X' => ['bg-gray-100 text-gray-600', 'Otro'],
                                    default => null,
                                };
                            @endphp
                            @if ($gBadge)
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $gBadge[0] }}">{{ $gBadge[1] }}</span>
                            @else
                                <span class="text-sm text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600">
                            {{ $player->birthdate?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600">
                            @if ($player->email)
                                <div>{{ $player->email }}</div>
                            @endif
                            @if ($player->phone)
                                <div class="text-gray-400">{{ $player->phone }}</div>
                            @endif
                            @if (!$player->email && !$player->phone)
                                —
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                @can('players.manage')
                                    <button wire:click="openEdit('{{ $player->id }}')"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition-colors"
                                            title="Editar">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 0 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete('{{ $player->id }}')"
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
                        <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">
                            @if ($search)
                                No se encontraron jugadores para "<strong>{{ $search }}</strong>".
                            @else
                                No hay jugadores registrados aún.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($players->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $players->links() }}
            </div>
        @endif
    </div>

    {{-- Modal crear / editar --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-gray-900/50" wire:click="$set('showModal', false)"></div>
            <div class="relative z-10 w-full max-w-lg rounded-2xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-900">
                        {{ $editingId ? 'Editar jugador' : 'Nuevo jugador' }}
                    </h2>
                    <button wire:click="$set('showModal', false)"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form wire:submit="save" class="space-y-4 px-6 py-5">

                    {{-- Nombre y Apellido --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="first_name" type="text" placeholder="Juan"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none focus:border-gray-400 transition-colors @error('first_name') border-red-400 bg-red-50 @enderror">
                            @error('first_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                                Apellido <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="last_name" type="text" placeholder="Pérez"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none focus:border-gray-400 transition-colors @error('last_name') border-red-400 bg-red-50 @enderror">
                            @error('last_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Documento / Género / Fecha --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Documento</label>
                            <input wire:model="document" type="text" placeholder="12345678"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none focus:border-gray-400 transition-colors">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Género</label>
                            <select wire:model="gender"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 outline-none focus:border-gray-400 transition-colors @error('gender') border-red-400 bg-red-50 @enderror">
                                <option value="">—</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                                <option value="X">Otro</option>
                            </select>
                            @error('gender')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Nacimiento</label>
                            <input wire:model="birthdate" type="date"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 outline-none focus:border-gray-400 transition-colors @error('birthdate') border-red-400 bg-red-50 @enderror">
                            @error('birthdate')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Contacto --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Teléfono</label>
                            <input wire:model="phone" type="text" placeholder="+595 981 000 000"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none focus:border-gray-400 transition-colors">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                            <input wire:model="email" type="email" placeholder="juan@ejemplo.com"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none focus:border-gray-400 transition-colors @error('email') border-red-400 bg-red-50 @enderror">
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
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
                        <h3 class="text-sm font-semibold text-gray-900">Eliminar jugador</h3>
                        <p class="mt-1 text-sm text-gray-500">Se eliminará el jugador y sus vínculos con equipos/parejas.</p>
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
