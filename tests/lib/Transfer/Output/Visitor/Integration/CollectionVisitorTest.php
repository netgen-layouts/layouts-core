<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Visitor\CollectionVisitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class CollectionVisitorTest extends VisitorTest
{
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Implementation requires sub-visitor');

        $this->getVisitor()->visit(new Collection());
    }

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
            [function (): Collection { return $this->collectionService->loadCollection(2); }, 'collection/collection_2.json'],
            [function (): Collection { return $this->collectionService->loadCollection(3); }, 'collection/collection_3.json'],
            [function (): Collection { return $this->collectionService->loadCollection(6); }, 'collection/collection_6.json'],
        ];
    }
}
