<?php
declare(strict_types=1);

namespace DodTest\Integration;

use DodLite\DocumentManager;
use DodLite\Extension\Flysystem\Adapter\FlysystemAdapter;
use DodLite\Synchronizer;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;

function createDocumentManager(): DocumentManager
{
    return new DocumentManager(
        new FlysystemAdapter(
            new Filesystem(
                new InMemoryFilesystemAdapter()
            )
        )
    );
}

test('Synchronizer works with FlysystemAdapter', function () {
    $sourceDocumentManager = createDocumentManager();
    $sourceDocumentManager->getCollection('pest')->writeData(1, ['pest1']);

    $targetDocumentManager = createDocumentManager();
    expect($targetDocumentManager->getCollection('pest')->hasDocumentById(1))->toBeFalse();

    $synchronizer = new Synchronizer($sourceDocumentManager, $targetDocumentManager);
    $synchronizer->synchronize();

    expect($targetDocumentManager->getCollection('pest')->hasDocumentById(1))->toBeTrue('Document #1 should have been synchronized to the target manager');
    expect($sourceDocumentManager->getCollection('pest')->hasDocumentById(1))->toBeTrue('Document #1 should still be in the source manager');
});

test('Synchronizer deletes work with FlysystemAdapter', function () {
    $sourceDocumentManager = createDocumentManager();
    $sourceDocumentManager->getCollection('pest')->writeData(1, ['pest1']);

    $targetDocumentManager = createDocumentManager();
    $targetDocumentManager->getCollection('pest')->writeData(2, ['pest2']);

    $synchronizer = new Synchronizer($sourceDocumentManager, $targetDocumentManager);
    $synchronizer->synchronize(synchronizeDeletes: false);

    expect($targetDocumentManager->getCollection('pest')->hasDocumentById(1))->toBeTrue('Document #1 should have been synchronized to the target manager');
    expect($targetDocumentManager->getCollection('pest')->hasDocumentById(2))->toBeTrue('Document #2 should still be in the target manager');

    $synchronizer->synchronize(synchronizeDeletes: true);
    expect($targetDocumentManager->getCollection('pest')->hasDocumentById(2))->toBeFalse('Document #2 should have been deleted from the target manager');
});
