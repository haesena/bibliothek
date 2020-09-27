<?php

namespace Controller;

use Psr\Container\ContainerInterface;

class Controller {
    /** @var ContainerInterface  */
    protected $container;
    /** @var \Database  */
    protected $db;
    /** @var \Slim\Views\PhpRenderer  */
    protected $view;
    /** @var \Slim\Interfaces\RouteParserInterface  */
    protected $router;
    protected $basePath;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
    	global $app;

        $this->container = $container;
        $this->db = $this->container->get('db');
		$this->view = $this->container->get('view');

		// Den Router aus der globalen $app auslesen
		$this->router = $app->getRouteCollector()->getRouteParser();
		$this->basePath = $app->getBasePath();

		// Da der router oft benutzt wird, wird dieser standardmässig an die View übergeben
		$this->view->addAttribute('router', $this->router);
    }

    protected function log($var, $title) {
    	if(strlen($title) > 0) {
    		print "<h3>{$title}</h3>";
    		print "<pre>".print_r($var, true)."</pre>";
    	}
    }

	protected function redirect($response, $location) {
		return $response
			->withHeader('Location', $this->basePath . $location)
			->withStatus(302);
	}

}