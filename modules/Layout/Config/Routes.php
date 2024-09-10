<?php

$routes->group('modules/backend/layouts', ["filter" => "cors", "namespace" => "\Modules\Layout\Controllers"], function ($routes) {
    $routes->get('new', 'Layout::new', ['as' => 'new-layout']);
    $routes->post('', 'Layout::create', ['as' => 'create-layout']);
    $routes->get('', 'Layout::index', ['as' => 'index-layout']);
    $routes->get('(:segment)/edit', 'Layout::edit/$1', ['as' => 'edit-layout']);
    $routes->post('(:segment)', 'Layout::update/$1', ['as' => 'update-layout']);
    $routes->get('check-delete/(:segment)', 'Layout::deleteConfirmation/$1', ['as' => 'delete-layout-confirmation']);
    $routes->delete('(:segment)', 'Layout::delete/$1', ['as' => 'delete-layout']);
});

$routes->group('modules/backend/layouts-details', ["filter" => "cors", "namespace" => "\Modules\Layout\Controllers"], function ($routes) {
    $routes->get('(:segment)/new', 'Layout::newDetails/$1', ['as' => 'new-layout-details']);
    $routes->post('', 'Layout::createDetails', ['as' => 'create-layout-details']);
    $routes->get('(:segment)/view', 'Layout::viewDetails/$1', ['as' => 'view-layout-details']);
});

// $routes->group('modules/backend/vehicles', ["filter" => "cors", "namespace" => "\Modules\Fleet\Controllers"], function ($routes) {
//     $routes->get('new', 'Vehicle::new', ['as' => 'new-vehicle']);
//     $routes->post('', 'Vehicle::create', ['as' => 'create-vehicle']);
//     $routes->get('', 'Vehicle::index', ['as' => 'index-vehicle']);
//     $routes->get('(:segment)/edit', 'Vehicle::edit/$1', ['as' => 'edit-vehicle']);
//     $routes->put('(:segment)', 'Vehicle::update/$1', ['as' => 'update-vehicle']);
//     $routes->delete('(:segment)', 'Vehicle::delete/$1', ['as' => 'delete-vehicle']);
//     $routes->get('trash', 'Vehicle::index/1', ['as' => 'trash-index-vehicle']);
//     $routes->get('restore/(:segment)', 'Vehicle::restore/$1', ['as' => 'restore-vehicle']);
// });

// $routes->group('modules/api/v1/frontend/fleets', ["filter" => "cors", "namespace" => "\Modules\Fleet\Controllers\Api"], function ($routes) {
//     $routes->get('', 'Fleet::index', ['as' => 'all-fleet']);
// });

// $routes->group('modules/api/v1/vehicles', ["filter" => "cors", "namespace" => "\Modules\Fleet\Controllers\Api"], function ($routes) {
//     $routes->get('', 'Vehicle::index', ['as' => 'all-vehicles']);
//     $routes->get('(:segment)', 'Vehicle::singleVehicle/$1', ['as' => 'single-vehicles']);
// });
