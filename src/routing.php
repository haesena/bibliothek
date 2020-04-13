<?php

$app->get('/', Controller\HomeController::class . ':getHome')->setName('home');

$app->get('/locations', Controller\LocationController::class . ':getLocations')->setName('locations');
$app->get('/locations/new', Controller\LocationController::class . ':newLocation')->setName('locations-new');
$app->post('/locations/save', Controller\LocationController::class . ':saveLocation')->setName('location-save');
$app->get('/locations/{id}', Controller\LocationController::class . ':getSingleLocation')->setName('location-detail');
$app->get('/locations/{id}/delete', Controller\LocationController::class . ':deleteLocation')->setName('location-delete');

$app->get('/books', Controller\BookController::class . ':getBooks')->setName('books');
$app->get('/books/new', Controller\BookController::class . ':newBook')->setName('book-new');
$app->post('/books/save', Controller\BookController::class . ':saveBook')->setName('book-save');
$app->get('/books/{id}', Controller\BookController::class . ':getSingleBook')->setName('book-detail');
$app->get('/books/{id}/delete', Controller\BookController::class . ':deleteBook')->setName('book-delete');


$app->get('/genre', Controller\GenreController::class . ':getGenre')->setName('genre');
$app->get('/genre/new', Controller\GenreController::class . ':newGenre')->setName('genre-new');
$app->post('/genre/save', Controller\GenreController::class . ':saveGenre')->setName('genre-save');
$app->get('/genre/{id}', Controller\GenreController::class . ':getSingleGenre')->setName('genre-detail');
$app->get('/genre/{id}/delete', Controller\GenreController::class . ':deleteGenre')->setName('genre-delete');