<?php

namespace Core;

/**
 * This class is a simple dependency injection container.
 * Class AppContainer
 * @package Core
 */
class AppContainer
{
    private static AppContainer $instance;
    private array $services = [];

    private function __construct()
    {
    }

    /**
     * Get the singleton instance of the AppContainer
     * @return AppContainer
     */
    public static function getInstance(): AppContainer
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Set a service in the container
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, mixed $value): void
    {
        $this->services[$key] = $value;
    }

    /**
     * Get a service from the container
     * @param string $key
     * @return mixed
     * @throws \Exception
     */
    public function get(string $key): mixed
    {
        if (!isset($this->services[$key])) {
            throw new \Exception("Service {$key} not found in container.");
        }

        return $this->services[$key];
    }

    // Prevent cloning and unserialization (which would create multiple instances)
    public function __clone()
    {
    }
    public function __wakeup()
    {
    }
}
