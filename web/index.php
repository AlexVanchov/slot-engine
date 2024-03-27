<?php
use Core\AppContainer;
use Core\Router;
use Core\Services\ConfigLoader;
use Core\SlotMachine;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

// Instantiate the dependency injection container
$appContainer = AppContainer::getInstance();

$appContainer->set('configLoader', new ConfigLoader());
$appContainer->set('slotMachine', new SlotMachine());
$appContainer->set('router', new Router());

$router = $appContainer->get('router');
// Register routes
$router->register('spin', 'GameController@index');
$router->direct();


