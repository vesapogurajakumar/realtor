<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Default page where all the projects categories will be shown
$routes->get('projects', 'Home::projects');

// Default page where contact screen will be shown
$routes->get('contact', 'Home::contact');

//To fetch contact form data
// This route is used to handle the form submission and send the data to the server
$routes->post('contactfetch', 'Home::contactfetch');

