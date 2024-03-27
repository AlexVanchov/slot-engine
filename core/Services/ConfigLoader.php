<?php

namespace Core\Services;

class ConfigLoader {
	private array $config;

	public function __construct() {
		// Adjust the file path as necessary to match your project structure
		$configPath = __DIR__ . '/../../config/config.json';
		if (!file_exists($configPath)) {
			throw new \Exception("Configuration file not found at: " . $configPath);
		}
		$this->config = json_decode(file_get_contents($configPath), true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \Exception("Error decoding JSON from config: " . json_last_error_msg());
		}
	}

	/**
	 * Get the configuration array.
	 * @return array
	 */
	public function getConfig(): array {
		return $this->config;
	}

	// Optional: Add specific getters for parts of the config you access frequently
	// This can simplify usage and reduce potential for typos in array keys throughout your code

	public function getTiles(): array {
		return $this->config['tiles'] ?? [];
	}

	public function getReels(): array {
		return $this->config['reels'][0] ?? []; // Assumes there's only one set of reels defined
	}

	public function getLines(): array {
		return $this->config['lines'] ?? [];
	}

	public function getPays(): array {
		return $this->config['pays'] ?? [];
	}

	// Prevent cloning and unserialization, which can create multiple instances
	public function __clone() {}
	public function __wakeup() {}
}
