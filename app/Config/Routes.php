<?php

use CodeIgniter\Router\RouteCollection;

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override('App\Controllers\Errors::show404');

/**
 * @var RouteCollection $routes
 */

// ---- Marketing / static pages ----
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');

// ---- Contact & lead capture ----
$routes->get('contact', 'Home::contactPage');
$routes->post('contact', 'Home::submitContact');
$routes->post('subscribe', 'Home::subscribe');
$routes->get('csrf', 'Home::csrf');   // fresh CSRF token for AJAX forms

// ---- Listings & property detail ----
$routes->get('listings', 'Listings::index');
$routes->match(['get', 'post'], 'api/properties', 'Listings::filter');   // AJAX filter endpoint
$routes->get('property/(:segment)', 'Listings::detail/$1');

// ---- Blog ----
$routes->get('blog', 'Blog::index');
$routes->post('blog/comment', 'Blog::comment');
$routes->get('blog/(:segment)', 'Blog::post/$1');

// ---- Admin (password-protected) ----
$routes->get('admin/login', 'Admin::login');
$routes->post('admin/login', 'Admin::attemptLogin');
$routes->get('admin/logout', 'Admin::logout');
$routes->group('admin', ['filter' => 'adminauth'], static function ($routes) {
    $routes->get('', 'Admin::dashboard');
    $routes->get('listings', 'Admin::listings');
    $routes->get('listings/new', 'Admin::createListing');
    $routes->post('listings', 'Admin::storeListing');
    $routes->post('listings/(:num)/delete', 'Admin::deleteListing/$1');
    $routes->get('leads', 'Admin::leads');
    $routes->get('leads/export', 'Admin::exportLeads');
    $routes->get('comments', 'Admin::comments');
    $routes->post('comments/(:num)/approve', 'Admin::approveComment/$1');
    $routes->post('comments/(:num)/unapprove', 'Admin::unapproveComment/$1');
    $routes->post('comments/(:num)/delete', 'Admin::deleteComment/$1');
});

// ---- SEO ----
$routes->get('sitemap.xml', 'Seo::sitemap');
$routes->get('robots.txt', 'Seo::robots');
