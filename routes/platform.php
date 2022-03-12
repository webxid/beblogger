<?php

declare(strict_types=1);

use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;
use App\Orchid\Screens;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/


#region Custom routes

// posts
Route::screen('posts/list', Screens\BeBlogger\PostsListScreen::class)
    ->name('platform.posts.list');

Route::screen('posts/add', Screens\BeBlogger\AddPostScreen::class)
    ->name('platform.posts.add');

Route::screen('posts/{course_id}/edit', Screens\BeBlogger\AddPostScreen::class)
    ->name('platform.posts.edit');


// Taxonomies
Route::screen('posts/taxonomies/list', Screens\BeBlogger\BasicClasses\BasicTaxonomiesListScreen::class)
    ->name('platform.posts.taxonomies.list');

Route::screen('posts/taxonomies/add', Screens\BeBlogger\BasicClasses\BasicAddTaxonomyScreen::class)
    ->name('platform.posts.taxonomies.add');

Route::screen('posts/taxonomies/{taxonomy_id}/edit', Screens\BeBlogger\BasicClasses\BasicTaxonomiesListScreen::class)
    ->name('platform.posts.taxonomies.edit');


#endregion
