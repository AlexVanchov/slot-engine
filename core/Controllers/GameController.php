<?php

namespace Core\Controllers;

use Core\AppContainer;
use Core\SlotMachine;

/**
 * Class GameController
 * @package Core\Controllers
 */
class GameController extends ControllerBase
{
    private SlotMachine $slotMachine;

    /**
     * GameController constructor.
     */
    public function __construct()
    {
        $appContainer = AppContainer::getInstance();
        $this->slotMachine = $appContainer->get('slotMachine');
    }

    /**
     * Index page for the slot machine game
     * @return void
     */
    public function index(): void
    {
        $stake = $_POST['stake'] ?? 1; // Example stake
        $result = $this->slotMachine->spin($stake); // Example stake

        // Use the render method from ControllerBase
        $this->render('slotMachine', ['result' => $result]);
    }
}
