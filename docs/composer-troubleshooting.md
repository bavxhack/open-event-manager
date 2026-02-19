# Composer Troubleshooting (PHP 8.2 / Symfony 7.4)

Wenn du Meldungen wie diese bekommst:

- `php composer.phar install` zeigt viele `Deprecated`-Hinweise
- `The lock file is not up to date with the latest changes in composer.json`
- `Your requirements could not be resolved ... php ^7.x`

liegt das in diesem Projekt typischerweise an **zwei Punkten**:

1. Es wird ein altes lokales `composer.phar` (Composer 1.x) benutzt.
2. `composer.lock` enthält alte Versionen (5.4/7.x-Pakete), während `composer.json` bereits auf Symfony 7.4 / PHP 8.2 steht.

## Empfohlene Befehle

Nutze Composer 2 (nicht `php composer.phar`):

```bash
composer --version
composer update --with-all-dependencies
composer install
```

## Docker

Der `app`-Container versucht beim ersten Start automatisch:

1. `composer install`
2. Fallback auf `composer update --with-all-dependencies`, falls der Lockfile-Stand nicht passt.

Damit wird `vendor/autoload.php` erstellt und Symfony kann starten.
