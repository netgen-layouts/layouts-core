<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\Collection\Collection as APICollection;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Collection\Collection as CollectionValue;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Transfer\Output\Visitor\Collection;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class CollectionTest extends VisitorTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->collectionService = $this->createCollectionService();
    }

    /**
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Implementation requires sub-visitor
     */
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor(): void
    {
        $this->getVisitor()->visit(new CollectionValue());
    }

    public function getVisitor(): VisitorInterface
    {
        return new Collection();
    }

    public function acceptProvider(): array
    {
        return [
            [new CollectionValue(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): APICollection { return $this->collectionService->loadCollection(2); }, 'collection/collection_2.json'],
            [function (): APICollection { return $this->collectionService->loadCollection(3); }, 'collection/collection_3.json'],
            [function (): APICollection { return $this->collectionService->loadCollection(6); }, 'collection/collection_6.json'],
        ];
    }
}
