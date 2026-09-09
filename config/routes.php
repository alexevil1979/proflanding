<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\LeadController;
use App\Controllers\PageController;
use App\Controllers\SitemapController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\LeadsController;
use App\Controllers\Admin\ServicesController;
use App\Controllers\Admin\PackagesController;
use App\Controllers\Admin\SettingsController;
use App\Controllers\Admin\SeoController;
use App\Controllers\Admin\NotificationsController;
use App\Controllers\Admin\PasswordController;

/** @var \App\Core\Router $router */

$router->get('/', [HomeController::class, 'index']);
$router->post('/lead', [LeadController::class, 'store']);
$router->get('/privacy', [PageController::class, 'privacy']);
$router->get('/offer', [PageController::class, 'offer']);
$router->get('/sitemap.xml', [SitemapController::class, 'index']);

$router->get('/admin/login', [AuthController::class, 'loginForm']);
$router->post('/admin/login', [AuthController::class, 'login']);
$router->post('/admin/logout', [AuthController::class, 'logout']);

$router->get('/admin', [DashboardController::class, 'index']);
$router->get('/admin/', [DashboardController::class, 'index']);

$router->get('/admin/leads', [LeadsController::class, 'index']);
$router->get('/admin/leads/{id}', [LeadsController::class, 'show']);
$router->post('/admin/leads/{id}', [LeadsController::class, 'update']);

$router->get('/admin/services', [ServicesController::class, 'index']);
$router->get('/admin/services/create', [ServicesController::class, 'create']);
$router->post('/admin/services/create', [ServicesController::class, 'store']);
$router->get('/admin/services/{id}/edit', [ServicesController::class, 'edit']);
$router->post('/admin/services/{id}/edit', [ServicesController::class, 'update']);
$router->post('/admin/services/{id}/delete', [ServicesController::class, 'delete']);
$router->post('/admin/services/reorder', [ServicesController::class, 'reorder']);

$router->get('/admin/packages', [PackagesController::class, 'index']);
$router->get('/admin/packages/create', [PackagesController::class, 'create']);
$router->post('/admin/packages/create', [PackagesController::class, 'store']);
$router->get('/admin/packages/{id}/edit', [PackagesController::class, 'edit']);
$router->post('/admin/packages/{id}/edit', [PackagesController::class, 'update']);
$router->post('/admin/packages/{id}/delete', [PackagesController::class, 'delete']);

$router->get('/admin/settings', [SettingsController::class, 'edit']);
$router->post('/admin/settings', [SettingsController::class, 'update']);

$router->get('/admin/seo', [SeoController::class, 'edit']);
$router->post('/admin/seo', [SeoController::class, 'update']);

$router->get('/admin/notifications', [NotificationsController::class, 'index']);
$router->post('/admin/notifications', [NotificationsController::class, 'update']);
$router->post('/admin/notifications/test-telegram', [NotificationsController::class, 'testTelegram']);
$router->post('/admin/notifications/test-email', [NotificationsController::class, 'testEmail']);

$router->get('/admin/password', [PasswordController::class, 'edit']);
$router->post('/admin/password', [PasswordController::class, 'update']);
