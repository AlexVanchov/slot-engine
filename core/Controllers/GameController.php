<?php
namespace Core\Controllers;

use Core\AppContainer;

class GameController extends ControllerBase {
	private $slotMachine;

	public function __construct() {
		$appContainer = AppContainer::getInstance();
		$this->slotMachine = $appContainer->get('slotMachine');
	}

	public function index(): void
	{
		$stake = $_POST['stake'] ?? 1; // Example stake
		$result = $this->slotMachine->spin($stake); // Example stake

		// Use the render method from ControllerBase
		$this->render('slotMachine', ['result' => $result]);
	}
}
