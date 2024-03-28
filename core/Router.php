<?php

namespace Core;

use Exception;

class Router {
	protected array $routes = [];
	private string $uri;

	/**
	 * Router constructor.
	 */
	public function __construct()
	{
		$this->uri = trim($_SERVER['REQUEST_URI'], '/');
	}
	/**
	 * Register a new route
	 *
	 * @param string $route
	 * @param string $action
	 * @return void
	 */
	public function register(string $route, string $action): void
	{
		$this->routes[$route] = $action;
	}

	/**
	 * Direct the request to the appropriate controller method
	 *
	 * @return void
	 * @throws Exception
	 */
	public function direct(): void
	{
		// check if router url it to web/assets OR vendor
		if (str_starts_with($this->uri, 'web')) {
			// Serve the requested asset
			$asset = __DIR__ . "/../{$this->uri}";
			if (file_exists($asset)) {
				header('Content-Type: ' . $_SERVER['HTTP_ACCEPT'] ?? 'text/plain');
				readfile($asset);
			} else {
				http_response_code(404);
				echo '404 Not Found';
			}
		} else {
			// Route the request
			if (array_key_exists($this->uri, $this->routes)) {
				[$controller, $method] = explode('@', $this->routes[$this->uri]);
				$this->callAction($controller, $method);
				return;
			}
			throw new Exception('No route defined for this URI.');
		}
	}

	/**
	 * @param $controller
	 * @param $method
	 * @return mixed
	 * @throws Exception
	 */
	protected function callAction($controller, $method): mixed
	{
		$controller = "Core\\Controllers\\{$controller}";
		$controller = new $controller;

		if (! method_exists($controller, $method)) {
			throw new Exception("{$controller} does not respond to the {$method} action.");
		}

		return $controller->$method();
	}
}
