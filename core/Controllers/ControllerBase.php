<?php
// core/Controllers/ControllerBase.php

namespace Core\Controllers;

abstract class ControllerBase {
	protected function render($viewName, $data = []) {
		extract($data);

		$viewPath = __DIR__ . '/../../views/' . $viewName . '.php';
		if (file_exists($viewPath)) {
			require $viewPath;
		} else {
			// Handle errors or set up a logging system for missing views
			echo "View cannot be found.";
		}
	}
}
