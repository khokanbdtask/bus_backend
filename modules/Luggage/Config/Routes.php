<?php

$routes->group('modules/backend/luggagesettings', ["filter" => "cors", "namespace" => "\Modules\Luggage\Controllers"], function ($routes) {
    $routes->get('new', 'Luggagesetting::new', ['as' => 'new-luggagesetting']);
    $routes->post('', 'Luggagesetting::create', ['as' => 'create-luggagesetting']);
    $routes->get('', 'Luggagesetting::index', ['as' => 'index-luggagesetting']);
    $routes->get('(:segment)/edit', 'Luggagesetting::edit/$1', ['as' => 'edit-luggagesetting']);
    $routes->put('(:segment)', 'Luggagesetting::update/$1', ['as' => 'update-luggagesetting']);
});
