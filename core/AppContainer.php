<?php

namespace Core;

class AppContainer
{
	private static $instance;
	private $services = [];

	private function __construct() {}

	public static function getInstance()
	{
		if (null === static::$instance) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public function set($key, $value)
	{
		$this->services[$key] = $value;
	}

	public function get($key)
	{
		if (!isset($this->services[$key])) {
			throw new \Exception("Service {$key} not found in container.");
		}

		return $this->services[$key];
	}

	// Prevent cloning and unserialization (which would create multiple instances)
	public function __clone() {}
	public function __wakeup() {}
}
