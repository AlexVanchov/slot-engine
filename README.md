# Slots-Game App
## Description
This is a PHP-based slots-game application, designed to simulate a slot 3x5 machine game. The application is built with a focus on back-end functionalities, including game logic and a simple front-end interface for interactions.

## Installation
To get started with this application, follow these steps:
- Clone the repository
- ```cd slot-engine```
### Install dependencies
Make sure you have Composer installed on your system. Then, run the following command in the project's root directory:
```bash
composer install
```
### Configure the application
Navigate to the config directory and adjust the configuration files according to your environment and preferences.

### Web server setup
Point your web server to the web directory.

### Usage
To play the slots game, simply go to the home page localhost/spin, where you will be presented with a base user interface to place bets and spin the slots. The core game logic will calculate wins based on the slot stake and it will show paylines, pattern matched, details about mystery(wild) symbol replacements, etc...

## Development
This project is structured as follows:

- views/: Holds the templates for rendering the game's user interface.
- core/: Includes essential functionalities and utilities.
- config/: Configuration file for game slot (lines, symbols, etc.).
- web/: The public web root directory, containing front-end assets.

## Code Sniffer
### Using PSR12
The framework uses PHP Code Sniffer to enforce coding standards. Run the following command to check for violations:
```bash
./vendor/bin/phpcs core/
```

### Auto Fixer
PHP Code Sniffer can automatically fix some violations. Run the following command to fix violations:
```bash
./vendor/bin/php-cs-fixer fix ./core
```