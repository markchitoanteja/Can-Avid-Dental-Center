<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Landing routes
$routes->get('/', 'Landing::index');
$routes->get('/home', 'Landing::home');
$routes->get('/about_us', 'Landing::about_us');
$routes->get('/services', 'Landing::services');
$routes->get('/contact_us', 'Landing::contact_us');

// Admin routes
$routes->get('/admin', 'Admin::admin');
$routes->get('/admin/dashboard', 'Admin::index');
$routes->get('/admin/appointments', 'Admin::appointments');
$routes->get('/admin/clients', 'Admin::clients');
$routes->get('/admin/services', 'Admin::services');
$routes->get('/admin/billing', 'Admin::billing');

$routes->post('/admin/update_profile', 'Admin::update_profile');
$routes->post('/admin/get_user_profile', 'Admin::get_user_profile');
$routes->post('/admin/add_appointment', 'Admin::add_appointment');
$routes->post('/admin/cancel_appointment', 'Admin::cancel_appointment');
$routes->post('/admin/get_appointment', 'Admin::get_appointment');
$routes->post('/admin/update_appointment', 'Admin::update_appointment');
$routes->post('/admin/add_client', 'Admin::add_client');
$routes->post('/admin/delete_client', 'Admin::delete_client');
$routes->post('/admin/get_client', 'Admin::get_client');
$routes->post('/admin/update_client', 'Admin::update_client');
$routes->post('/admin/add_service', 'Admin::add_service');
$routes->post('/admin/delete_service', 'Admin::delete_service');
$routes->post('/admin/get_service', 'Admin::get_service');
$routes->post('/admin/update_service', 'Admin::update_service');
$routes->post('/admin/add_billing', 'Admin::add_billing');

$routes->get('/print_billing/(:num)', 'Admin::print_billing/$1');

// Client routes
$routes->get('/client/dashboard', 'Client::dashboard');
$routes->get('/client/profile', 'Client::profile');
$routes->get('/client/appointments', 'Client::appointments');
$routes->get('/client/messages', 'Client::messages');

$routes->post('/client/update_profile', 'Client::update_profile');
$routes->post('/client/change_password', 'Client::change_password');
$routes->post('/client/add_appointment', 'Client::add_appointment');
$routes->post('/client/appointments/delete/(:num)', 'Client::delete_appointment/$1');
$routes->post('/client/messages/mark-read', 'Client::markMessageRead');
$routes->post('/client/messages/delete/(:num)', 'Client::delete_message/$1');

// Auth routes
$routes->post('/login', 'Auth::login');
$routes->post('/register', 'Auth::register');
$routes->post('/update_profile', 'Auth::update_profile');
$routes->post('/logout', 'Auth::logout');