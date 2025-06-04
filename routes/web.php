<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\RoomieMatchController;
use App\Http\Controllers\AcademicInfoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Página principal
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/ocio', function () {
    $events = App\Models\Event::public()->upcoming()->get();
    $places = App\Models\Place::all();
    return view('events.index', compact('events', 'places'));
})->name('ocio');

// Rutas de autenticación (se instalarán con Laravel UI)
Auth::routes(['verify' => true]);

// Dashboard principal
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rutas protegidas por autenticación
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Mis anuncios y eventos
    Route::get('/my-listings', [ListingController::class, 'myListings'])->name('listings.my');
    Route::get('/my-events', [EventController::class, 'myEvents'])->name('events.my');
    
    // Toggle de disponibilidad/visibilidad
    Route::patch('/listings/{listing}/toggle-availability', [ListingController::class, 'toggleAvailability'])
        ->name('listings.toggle-availability');
    Route::patch('/events/{event}/toggle-visibility', [EventController::class, 'toggleVisibility'])
        ->name('events.toggle-visibility');
    
    // Registro de asistencia a eventos
    Route::post('/events/{event}/register', [EventController::class, 'registerAttendance'])
        ->name('events.register');
    Route::delete('/events/{event}/unregister', [EventController::class, 'unregisterAttendance'])
        ->name('events.unregister');

    // Reservar visita a un alojamiento
    Route::post('/listings/{listing}/schedule-visit', [ListingController::class, 'scheduleVisit'])
        ->name('listings.schedule');
});

// Rutas de recursos principales (algunas públicas)
Route::resource('listings', ListingController::class);
Route::resource('events', EventController::class);
Route::resource('places', PlaceController::class);

// RoomieMatch landing and functionality
Route::get('/roomie-match', [RoomieMatchController::class, 'index'])->name('roomie.index');
Route::middleware(['auth', 'verified'])->prefix('roomie-match')->name('roomie.')->group(function () {
    Route::post('/like/{user}', [RoomieMatchController::class, 'like'])->name('like');
    Route::post('/message/{match}', [RoomieMatchController::class, 'sendMessage'])->name('message');
    Route::get('/conversation/{match}', [RoomieMatchController::class, 'conversation'])->name('conversation');
});

// Rutas de información académica
Route::prefix('academic')->name('academic.')->group(function () {
    Route::get('/', [AcademicInfoController::class, 'index'])->name('index');
    Route::get('/scholarships', [AcademicInfoController::class, 'scholarships'])->name('scholarships');
    Route::get('/cut-off-marks', [AcademicInfoController::class, 'cutOffMarks'])->name('cut-off-marks');
    Route::get('/cut-off-calculator', [AcademicInfoController::class, 'calculator'])->name('calculator');
    Route::get('/search', [AcademicInfoController::class, 'search'])->name('search');
    
    // Rutas protegidas para preferencias académicas
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/preferences', [AcademicInfoController::class, 'preferences'])->name('preferences');
        Route::post('/preferences', [AcademicInfoController::class, 'updatePreferences'])->name('preferences.update');
        Route::get('/recommended', [AcademicInfoController::class, 'recommended'])->name('recommended');
    });
});

// Rutas AJAX para búsquedas por ubicación
Route::middleware(['auth'])->group(function () {
    Route::post('/listings/search-by-location', [ListingController::class, 'searchByLocation'])
        ->name('listings.search-location');
    Route::post('/events/search-by-location', [EventController::class, 'searchByLocation'])
        ->name('events.search-location');
    Route::post('/places/search-by-location', [PlaceController::class, 'searchByLocation'])
        ->name('places.search-location');
});

// Rutas AJAX para estadísticas
Route::get('/api/listings/statistics', [ListingController::class, 'statistics'])
    ->name('api.listings.statistics');
Route::get('/api/events/statistics', [EventController::class, 'statistics'])
    ->name('api.events.statistics');
Route::get('/api/events/recommended', [EventController::class, 'recommended'])
    ->middleware('auth')
    ->name('api.events.recommended');

// Rutas de administración
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Gestión de usuarios
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    
    // Gestión de contenido
    Route::get('/listings', [AdminController::class, 'listings'])->name('listings');
    Route::get('/events', [AdminController::class, 'events'])->name('events');
    Route::get('/places', [AdminController::class, 'places'])->name('places');
    
    // Verificación de lugares
    Route::patch('/places/{place}/verify', [AdminController::class, 'verifyPlace'])->name('places.verify');
    
    // Gestión de información académica
    Route::resource('academic-info', AdminAcademicInfoController::class, [
        'names' => [
            'index' => 'admin.academic-info.index',
            'create' => 'admin.academic-info.create',
            'store' => 'admin.academic-info.store',
            'show' => 'admin.academic-info.show',
            'edit' => 'admin.academic-info.edit',
            'update' => 'admin.academic-info.update',
            'destroy' => 'admin.academic-info.destroy',
        ]
    ]);
});

// Rutas de configuración y utilidades
Route::middleware(['auth'])->group(function () {
    // Configuración de notificaciones
    Route::get('/settings/notifications', [SettingsController::class, 'notifications'])
        ->name('settings.notifications');
    Route::post('/settings/notifications', [SettingsController::class, 'updateNotifications'])
        ->name('settings.notifications.update');
    
    // Exportar datos del usuario
    Route::get('/export/my-data', [ExportController::class, 'exportUserData'])
        ->name('export.user-data');
});

// Rutas de ayuda y soporte
Route::get('/help', function () {
    return view('help.index');
})->name('help');

Route::get('/privacy', function () {
    return view('legal.privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('legal.terms');
})->name('terms');

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Fallback para rutas no encontradas
Route::fallback(function () {
    return view('errors.404');
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
