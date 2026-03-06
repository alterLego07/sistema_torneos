<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('tournaments.index') }}" wire:navigate
           class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition-colors">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Nuevo torneo</h1>
            <p class="mt-0.5 text-sm text-gray-500">Completá los datos para crear el torneo.</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
        <form wire:submit="save" class="divide-y divide-gray-100">

            {{-- Información básica --}}
            <div class="px-6 py-5">
                <h2 class="mb-4 text-sm font-semibold text-gray-700">Información básica</h2>
                <div class="space-y-4">

                    {{-- Nombre --}}
                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="name" id="name" type="text"
                               placeholder="Copa Chacomer 2026"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none focus:border-gray-400 transition-colors @error('name') border-red-400 bg-red-50 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Temporada --}}
                    <div>
                        <label for="season" class="mb-1.5 block text-sm font-medium text-gray-700">
                            Temporada
                        </label>
                        <input wire:model="season" id="season" type="text"
                               placeholder="2026 / Apertura 2026"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 outline-none focus:border-gray-400 transition-colors">
                        <p class="mt-1 text-xs text-gray-400">Opcional. Ej: "2026", "Apertura 2026".</p>
                    </div>
                </div>
            </div>

            {{-- Fechas --}}
            <div class="px-6 py-5">
                <h2 class="mb-4 text-sm font-semibold text-gray-700">Período</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="starts_on" class="mb-1.5 block text-sm font-medium text-gray-700">Fecha de inicio</label>
                        <input wire:model="starts_on" id="starts_on" type="date"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 outline-none focus:border-gray-400 transition-colors @error('starts_on') border-red-400 bg-red-50 @enderror">
                        @error('starts_on')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="ends_on" class="mb-1.5 block text-sm font-medium text-gray-700">Fecha de fin</label>
                        <input wire:model="ends_on" id="ends_on" type="date"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 outline-none focus:border-gray-400 transition-colors @error('ends_on') border-red-400 bg-red-50 @enderror">
                        @error('ends_on')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Estado --}}
            <div class="px-6 py-5">
                <h2 class="mb-4 text-sm font-semibold text-gray-700">Estado inicial</h2>
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ([
                        ['draft',     'Borrador',   'El torneo no es visible aún.',         'bg-gray-100 text-gray-600'],
                        ['published', 'Publicado',  'Visible y abierto a inscripciones.',   'bg-blue-50 text-blue-700'],
                        ['running',   'En curso',   'Competencia activa.',                  'bg-green-50 text-green-700'],
                        ['finished',  'Finalizado', 'Torneo concluido.',                    'bg-orange-50 text-orange-700'],
                    ] as [$value, $label, $description, $badgeClass])
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition-colors
                                      {{ $status === $value ? 'border-gray-900 bg-gray-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input wire:model="status" type="radio" value="{{ $value }}"
                                   class="mt-0.5 h-4 w-4 accent-gray-900">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $label }}</span>
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $badgeClass }}">{{ $label }}</span>
                                </div>
                                <p class="mt-0.5 text-xs text-gray-500">{{ $description }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('status')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Acciones --}}
            <div class="flex items-center justify-between px-6 py-4">
                <a href="{{ route('tournaments.index') }}" wire:navigate
                   class="rounded-xl px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                    <span wire:loading.remove wire:target="save">Crear torneo</span>
                    <span wire:loading wire:target="save">Creando…</span>
                </button>
            </div>
        </form>
    </div>

</div>
