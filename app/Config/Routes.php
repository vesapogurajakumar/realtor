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

// ---- Listings & property detail ----
$routes->get('listings', 'Listings::index');
$routes->match(['get', 'post'], 'api/properties', 'Listings::filter');   // AJAX filter endpoint
$routes->get('property/(:segment)', 'Listings::detail/$1');

// ---- Blog ----
$routes->get('blog', 'Blog::index');
$routes->post('blog/comment', 'Blog::comment');
$routes->get('blog/(:segment)', 'Blog::post/$1');

// ---- SEO ----
$routes->get('sitemap.xml', 'Seo::sitemap');
$routes->get('robots.txt', 'Seo::robots');
