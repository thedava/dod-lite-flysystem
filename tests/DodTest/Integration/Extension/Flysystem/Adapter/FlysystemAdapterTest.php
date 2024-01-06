<?php
declare(strict_types=1);

namespace DodTest\Integration\Extension\Flysystem\Adapter;

use DodLite\Exceptions\NotFoundException;
use DodLite\Extension\Flysystem\Adapter\FlysystemAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;

function createFlysystemAdapter(): FlysystemAdapter
{
    return new FlysystemAdapter(
        new Filesystem(
            new InMemoryFilesystemAdapter()
        )
    );
}

test('Reading non-existing data throws exception', function (): void {
    $flysystemAdapter = createFlysystemAdapter();

    $flysystemAdapter->read('collection', 'key');
})->throws(NotFoundException::class);

test('Writing and Reading data works', function (): void {
    $flysystemAdapter = createFlysystemAdapter();

    $flysystemAdapter->write('collection', 'key', ['data' => 'value']);
    $data = $flysystemAdapter->read('collection', 'key');

    expect($data)->toBe(['data' => 'value']);
});

test('Deleting data works', function (): void {
    $flysystemAdapter = createFlysystemAdapter();

    $flysystemAdapter->write('collection', 'key', ['data' => 'value']);
    expect($flysystemAdapter->has('collection', 'key'))->toBeTrue();
    expect($flysystemAdapter->read('collection', 'key'))->toBe(['data' => 'value']);

    $flysystemAdapter->delete('collection', 'key');
    expect($flysystemAdapter->has('collection', 'key'))->toBeFalse();
});

test('readAll works', function (): void {
    $flysystemAdapter = createFlysystemAdapter();

    $flysystemAdapter->write('collection', 'key', ['data' => 'value']);
    $flysystemAdapter->write('collection', 'key2', ['data' => 'value2']);

    $documents = iterator_to_array($flysystemAdapter->readAll('collection'));
    expect($documents)
        ->toHaveKey('key')
        ->toHaveKey('key2');
});


test('readAll without data works', function (): void {
    $flysystemAdapter = createFlysystemAdapter();

    $documents = iterator_to_array($flysystemAdapter->readAll('collection'));
    expect($documents)->toBe([]);
});

test('getAllCollectionNames works', function () {
    $flysystemAdapter = createFlysystemAdapter();

    $flysystemAdapter->write('collection1', 'key', ['data' => 'value']);
    $flysystemAdapter->write('collection2', 'key', ['data' => 'value']);
    $flysystemAdapter->write('collection3', 'key', ['data' => 'value']);

    $collectionNames = iterator_to_array($flysystemAdapter->getAllCollectionNames());
    expect($collectionNames)->toContain('collection1', 'collection2', 'collection3');
});
