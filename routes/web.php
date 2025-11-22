<?php

use App\Http\Controllers\Apify\CrawlController;
use App\Http\Controllers\Apify\DataCrawlController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Settings\BlogCategoryController;
use App\Http\Controllers\Settings\CityController;
use App\Http\Controllers\Settings\SectionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Access\UserController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Settings\PrivacyPolicyController;
use App\Http\Controllers\Front\PrivacyPolicyController as FrontPrivacyPolicy;
use App\Http\Controllers\Access\RoleController;
use App\Http\Controllers\Access\PermissionController;
use App\Http\Controllers\Access\UserRoleController;
use App\Http\Controllers\Settings\ContentSettingController;
use App\Http\Controllers\Settings\PlaceSettingController;
use App\Http\Controllers\Settings\TagController;
use App\Http\Controllers\Settings\UrlCrawlController;
use App\Http\Controllers\Blog\BlogFrontController;
use App\Http\Controllers\Auth\CustomAuthenticatedSessionController;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/privacy-policy', [FrontPrivacyPolicy::class, 'index'])->name('privacy.index');
Route::get('/blogs', [BlogFrontController::class, 'index'])->name('blogs.front.index');
Route::get('/blogs/{slug}', [BlogFrontController::class, 'show'])->name('blogs.front.show');
Route::get('/embed/{id}', [ContentController::class, 'embed'])->name('embed.view');

Route::middleware(['auth'])->group(function () {
    // Route::autoPermission()->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::prefix('access')->group(function () {
        // Role management
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        // Permission management
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

        // Assign roles & permissions
        Route::post('/assign/role', [UserRoleController::class, 'assignRole'])->name('assign.role');
    });

    Route::prefix('access')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::prefix('admin/blogs')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('blogs.index');
        Route::get('/create', [BlogController::class, 'create'])->name('blogs.create');
        Route::post('/', [BlogController::class, 'store'])->name('blogs.store');
        Route::get('/{blog}/edit', [BlogController::class, 'edit'])->name('blogs.edit');
        Route::put('/{blog}', [BlogController::class, 'update'])->name('blogs.update');
        Route::delete('/{blog}', [BlogController::class, 'destroy'])->name('blogs.destroy');
    });

    Route::prefix('settings')->group(function () {});

    Route::prefix('settings')->group(function () {
        // Tempat untuk menampilkan daftar menu utama
        Route::get('/', fn() => redirect()->route('settings.places.index'));

        // Place
        Route::get('/places', [PlaceSettingController::class, 'index'])->name('settings.places.index');
        Route::post('/places', [PlaceSettingController::class, 'store']);
        Route::delete('/places/{id}', [PlaceSettingController::class, 'destroy']);

        // Category
        Route::get('/content-categories', [ContentSettingController::class, 'index'])->name('content-categories.index');
        Route::get('/content-categories/create', [ContentSettingController::class, 'create'])->name('content-categories.create');
        Route::post('/content-categories', [ContentSettingController::class, 'store'])->name('content-categories.store');
        Route::get('/content-categories/{id}/edit', [ContentSettingController::class, 'edit'])->name('content-categories.edit');
        Route::put('/content-categories/{id}', [ContentSettingController::class, 'update'])->name('content-categories.update');
        Route::delete('/content-categories/{id}', [ContentSettingController::class, 'destroy'])->name('content-categories.destroy');

        // URL Crawl
        Route::get('/url-crawls', [UrlCrawlController::class, 'index'])->name('settings.urlCrawls.index');
        Route::post('/url-crawls', [UrlCrawlController::class, 'store'])->name('settings.urlCrawls.store');
        Route::get('/url-crawls/{url_crawl}/edit', [UrlCrawlController::class, 'edit'])->name('settings.urlCrawls.edit');
        Route::put('/url-crawls/{url_crawl}', [UrlCrawlController::class, 'update'])->name('settings.urlCrawls.update');
        Route::delete('/url-crawls/{url_crawl}', [UrlCrawlController::class, 'destroy'])->name('settings.urlCrawls.destroy');

        // Run crawl (Apify)
        Route::post('/url-crawls/{id}/run', [CrawlController::class, 'run'])->name('settings.urlCrawls.run');
        Route::post('/url-crawls/run-all', [CrawlController::class, 'runAll'])->name('settings.url-crawls.run-all');

        Route::get('/data-crawling', [DataCrawlController::class, 'index'])->name('settings.crawls.index');
        Route::get('/data-crawling/{id}/show', [DataCrawlController::class, 'show'])->name('settings.crawls.show');
        Route::get('/data-crawling/create', [DataCrawlController::class, 'create'])->name('settings.crawls.create');
        Route::post('/data-crawling', [DataCrawlController::class, 'store'])->name('contents.store');

        Route::get('/content', [ContentController::class, 'index'])->name('content.index');
        Route::post('/content/publish', [ContentController::class, 'publish'])->name('content.publish');
        Route::post('/content/publish-selected', [ContentController::class, 'publishSelected'])->name('content.publish.selected');
        Route::get('/content/{content}/edit', [ContentController::class, 'create'])->name('content.create');
        Route::put('/content/{content}/edit', [ContentController::class, 'update'])->name('content.update');

        // Tags
        Route::get('/tags', [TagController::class, 'index'])->name('settings.tags.index');
        Route::post('/tags', [TagController::class, 'store']);
        Route::delete('/tags/{id}', [TagController::class, 'destroy']);

        // city
        Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
        Route::get('/cities/create', [CityController::class, 'create'])->name('cities.create');
        Route::post('/cities', [CityController::class, 'store'])->name('cities.store');
        Route::get('/cities/{city}/edit', [CityController::class, 'edit'])->name('cities.edit');
        Route::put('/cities/{city}', [CityController::class, 'update'])->name('cities.update');
        Route::delete('/cities/{city}', [CityController::class, 'destroy'])->name('cities.destroy');
        Route::post('/cities/{city}/default', [CityController::class, 'makeDefault'])->name('cities.default');

        // blog category
        Route::get('/blog-categories', [BlogCategoryController::class, 'index'])->name('blog-categories.index');
        Route::get('/blog-categories/create', [BlogCategoryController::class, 'create'])->name('blog-categories.create');
        Route::post('/blog-categories', [BlogCategoryController::class, 'store'])->name('blog-categories.store');
        Route::get('/blog-categories/{id}/edit', [BlogCategoryController::class, 'edit'])->name('blog-categories.edit');
        Route::put('/blog-categories/{id}', [BlogCategoryController::class, 'update'])->name('blog-categories.update');
        Route::delete('/blog-categories/{id}', [BlogCategoryController::class, 'destroy'])->name('blog-categories.destroy');

        // Section
        Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
        Route::post('/sections', [SectionController::class, 'store'])->name('sections.store');
        Route::put('/sections/{section}', [SectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');
        Route::post('/sections/reorder', [SectionController::class, 'reorder'])->name('sections.reorder');

        Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy.index');
        Route::put('/privacy-policy', [PrivacyPolicyController::class, 'update'])->name('privacy.update');
    });
});
// });

Route::post('/login', [CustomAuthenticatedSessionController::class, 'store'])
    ->middleware(['guest'])
    ->name('login');
Route::get('/help', function () {
    return inertia('Help');
})->name('help');
