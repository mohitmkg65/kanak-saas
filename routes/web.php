<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OrganizationRegisterController;
use App\Http\Controllers\Auth\OrganizationSelectController;
use App\Http\Controllers\Organization;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Organization Self-Registration
    Route::get('/register', [OrganizationRegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [OrganizationRegisterController::class, 'register']);
});

// Authenticated General Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/select-organization', [OrganizationSelectController::class, 'showSelectForm'])->name('organization.select');
    Route::post('/select-organization', [OrganizationSelectController::class, 'select'])->name('organization.select.submit');
});

// Super Admin Context Routes (/admin/*)
Route::middleware(['auth', 'super.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Organizations — with approval workflow actions
    Route::resource('organizations', Admin\OrganizationController::class);
    Route::post('organizations/{organization}/approve', [Admin\OrganizationController::class, 'approve'])->name('organizations.approve');
    Route::post('organizations/{organization}/reject', [Admin\OrganizationController::class, 'reject'])->name('organizations.reject');
    Route::post('organizations/{organization}/review', [Admin\OrganizationController::class, 'markUnderReview'])->name('organizations.review');
    Route::post('organizations/{organization}/suspend', [Admin\OrganizationController::class, 'suspend'])->name('organizations.suspend');

    Route::resource('users', Admin\UserController::class)->except(['show', 'destroy']);
    Route::resource('roles', Admin\RoleController::class)->except(['show']);

    // Global Masters
    Route::resource('categories', Admin\CategoryController::class)->except(['show']);
    Route::resource('units', Admin\UnitController::class)->except(['show']);
    Route::resource('brands', Admin\BrandController::class)->except(['show']);
    Route::resource('products', Admin\ProductController::class);

    // Customer Directory (read-only across all orgs)
    Route::get('customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [Admin\CustomerController::class, 'show'])->name('customers.show');

    Route::get('/settings', [Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');

    Route::get('/profile', [Admin\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [Admin\ProfileController::class, 'changePassword'])->name('profile.password');
});

// Organization Context Routes (/organization/*)
Route::middleware(['auth', 'org.context', 'org.membership'])->prefix('organization')->name('organization.')->group(function () {
    Route::get('/dashboard', [Organization\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', Organization\UserController::class)->except(['show', 'destroy']);
    Route::resource('roles', Organization\RoleController::class)->except(['show']);

    // Customer Management (tenant-isolated)
    Route::resource('customers', Organization\CustomerController::class);

    // Organization Products (hybrid: catalog-linked + custom)
    Route::get('products/catalog', [Organization\ProductController::class, 'catalog'])->name('products.catalog');
    Route::get('products/catalog/{product}/details', [Organization\ProductController::class, 'catalogProductDetails'])->name('products.catalog.details');
    Route::resource('products', Organization\ProductController::class);

    Route::get('/settings', [Organization\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [Organization\SettingController::class, 'update'])->name('settings.update');

    Route::get('/profile', [Organization\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [Organization\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [Organization\ProfileController::class, 'changePassword'])->name('profile.password');
});
