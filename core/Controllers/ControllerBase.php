<?php

namespace Core\Controllers;

/**
 * Class ControllerBase
 * @package Core\Controllers
 */
abstract class ControllerBase
{
    /**
     * Render a view
     * @param string $viewName
     * @param array $data
     * @return void
     */
    protected function render(string $viewName, array $data = []): void
    {
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
