<?php

$app->get('/', Controller\HomeController::class . ':getHome')->setName('home');

$app->get('/locations', Controller\LocationController::class . ':getLocations')->setName('locations');
$app->get('/locations/{id}', Controller\LocationController::class . ':getSingleLocation')->setName('location-detail');

$app->get('/books', Controller\BookController::class . ':getBooks')->setName('books');
$app->get('/books/new', Controller\BookController::class . ':newBook')->setName('book-new');
$app->get('/books/{id}', Controller\BookController::class . ':getSingleBook')->setName('book-detail');
