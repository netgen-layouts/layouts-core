<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Collection\Query as QueryValue;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Transfer\Output\Visitor\Query;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

abstract class QueryTest extends VisitorTest
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
        $this->getVisitor()->visit(new QueryValue());
    }

    public function getVisitor(): VisitorInterface
    {
        return new Query($this->collectionService);
    }

    public function acceptProvider(): array
    {
        return [
            [new QueryValue(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider(): array
    {
        return [
            [function (): APIQuery { return $this->collectionService->loadQuery(1); }, 'query/query_1.json'],
            [function (): APIQuery { return $this->collectionService->loadQuery(2); }, 'query/query_2.json'],
            [function (): APIQuery { return $this->collectionService->loadQuery(4); }, 'query/query_4.json'],
        ];
    }
}
