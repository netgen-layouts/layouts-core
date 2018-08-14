<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Transfer\Output\Visitor\QueryVisitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class QueryVisitorTest extends VisitorTest
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
        $this->getVisitor()->visit(new Query());
    }

    public function getVisitor(): VisitorInterface
    {
        return new QueryVisitor($this->collectionService);
    }

    public function acceptProvider(): array
    {
        return [
            [new Query(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): Query { return $this->collectionService->loadQuery(1); }, 'query/query_1.json'],
            [function (): Query { return $this->collectionService->loadQuery(2); }, 'query/query_2.json'],
            [function (): Query { return $this->collectionService->loadQuery(4); }, 'query/query_4.json'],
        ];
    }
}
