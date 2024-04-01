# slot engine

## Code Sniffer
### Using PSR12
The framework uses PHP Code Sniffer to enforce coding standards. Run the following command to check for violations:

```bash
./vendor/bin/phpcs app/
```

### Auto Fixer
PHP Code Sniffer can automatically fix some violations. Run the following command to fix violations:
```bash
./vendor/bin/php-cs-fixer fix ./app
```