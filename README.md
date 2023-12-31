# dod-lite-flysystem

[![.github/workflows/tests.yml](https://github.com/thedava/dod-lite-flysystem/actions/workflows/tests.yml/badge.svg)](https://github.com/thedava/dod-lite-flysystem/actions/workflows/tests.yml)

A [Flysystem](https://github.com/thephpleague/flysystem) adapter for [dod-lite](https://github.com/thedava/dod-lite)

## Installation

via Composer

```bash
composer require thedava/dod-lite-flysystem
```

## Flysystem

The `FlysystemAdapter` uses the `League\Flysystem` to provide a simple way to store data. The usage is pretty simple:

```php
// Store data locally in files
$documentManager = new \DodLite\DocumentManager(
    new \DodLite\Extension\Flysystem\Adapter\FlysystemAdapter(
        new \League\Flysystem\Filesystem(
            new \League\Flysystem\Local\LocalFilesystemAdapter(
               '/path/to/your/storage'
            )
        )
    )
);
```
