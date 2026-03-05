<div class="space-y-6">
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-sm text-gray-600">
            Bienvenido al sistema de torneos. Acá vas a tener métricas y accesos rápidos.
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <div class="text-sm text-gray-500">Torneos</div>
            <div class="mt-2 text-2xl font-semibold text-gray-900">—</div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <div class="text-sm text-gray-500">Partidos</div>
            <div class="mt-2 text-2xl font-semibold text-gray-900">—</div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <div class="text-sm text-gray-500">Jugadores</div>
            <div class="mt-2 text-2xl font-semibold text-gray-900">—</div>
        </div>

        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <div class="text-sm text-gray-500">Categorías</div>
            <div class="mt-2 text-2xl font-semibold text-gray-900">—</div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Accesos rápidos</h2>

        <div class="mt-4 flex flex-wrap gap-2">
            @can('tournaments.create')
                <a href="{{ route('tournaments.create') }}"
                   class="inline-flex items-center rounded-xl bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    Crear torneo
                </a>
            @endcan

            @can('matches.schedule')
                <a href="{{ route('matches.scheduler') }}"
                   class="inline-flex items-center rounded-xl bg-white px-4 py-2 text-sm font-medium text-gray-900 ring-1 ring-gray-200 hover:bg-gray-50">
                    Generar fixture
                </a>
            @endcan

            @can('results.enter')
                <a href="{{ route('matches.results') }}"
                   class="inline-flex items-center rounded-xl bg-white px-4 py-2 text-sm font-medium text-gray-900 ring-1 ring-gray-200 hover:bg-gray-50">
                    Cargar resultados
                </a>
            @endcan
        </div>
    </div>
</div>