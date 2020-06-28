<?php


/**
 * Beispiel: http://www.slimframework.com/docs/v3/tutorial/first-app.html
 */

// Start the session
session_start();

require __DIR__ . '/vendor/autoload.php';

use DI\Container;
use Slim\Factory\AppFactory;

// Container = Variable die überall zur verfügung stehen
$container = new Container();

// Datenbank initialisieren
$container->set('db', function () {
	global $config;
    return new Database($config['db_dsn'], $config['db_username'], $config['db_password']);
});

// View für HTML Ausgabe initialisieren
$container->set('view', function () {
	$renderer = new \Slim\Views\PhpRenderer(__DIR__ . '/templates/');
	$renderer->setLayout("page.phtml");
    return $renderer;
});

// Slim Framework initialisiere
AppFactory::setContainer($container);
$app = AppFactory::create();


$app->setBasePath('/bibliothek');


// die routen werden in routing.php ausgelagert damit es übersichtlicher ist
require __DIR__ . '/routing.php';

$app->run();
