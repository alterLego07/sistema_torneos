<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;

Route::view('/', 'welcome');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/tournaments', \App\Livewire\Tournaments\Index::class)->name('tournaments.index');
    Route::get('/tournaments/create', \App\Livewire\Tournaments\Form::class)->name('tournaments.create');

    Route::get('/disciplines', \App\Livewire\Disciplines\Manager::class)->name('disciplines.manager');
    Route::get('/categories', \App\Livewire\Categories\Manager::class)->name('categories.manager');

    Route::get('/players', \App\Livewire\Players\Index::class)->name('players.index');
    Route::get('/participants', \App\Livewire\Participants\Index::class)->name('participants.index');
    // Route::get('/registrations', \App\Livewire\Categories\Registrations::class)->name('registrations.index');

    Route::get('/matches/calendar', \App\Livewire\Matches\Calendar::class)->name('matches.calendar');
    Route::get('/matches/scheduler', \App\Livewire\Matches\Scheduler::class)->name('matches.scheduler');
    Route::get('/matches/results', \App\Livewire\Matches\ResultEntry::class)->name('matches.results');

    Route::get('/standings', \App\Livewire\Standings\Table::class)->name('standings.table');
    Route::get('/brackets', \App\Livewire\Brackets\View::class)->name('brackets.view');


    // Admin example
    Route::get('/admin/users', fn() => 'TODO Users')->name('admin.users');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');
require __DIR__ . '/auth.php';
