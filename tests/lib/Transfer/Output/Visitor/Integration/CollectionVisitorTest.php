<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Transfer\Output\Visitor\CollectionVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Ramsey\Uuid\Uuid;

abstract class CollectionVisitorTest extends VisitorTest
{
    public function getVisitor(): VisitorInterface
    {
        return new CollectionVisitor();
    }

    public function acceptProvider(): array
    {
        return [
            [new Collection(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): Collection { return $this->collectionService->loadCollection(Uuid::fromString('45a6e6f5-0ae7-588b-bf2a-0e4cc24ec60a')); }, 'collection/collection_2.json'],
            [function (): Collection { return $this->collectionService->loadCollection(Uuid::fromString('da050624-8ae0-5fb9-ae85-092bf8242b89')); }, 'collection/collection_3.json'],
            [function (): Collection { return $this->collectionService->loadCollection(Uuid::fromString('00872ad1-60e2-5947-95c2-e2eb75427af6')); }, 'collection/collection_6.json'],
        ];
    }
}
