<?php

namespace Netgen\BlockManager\Tests\Transfer\Output\Visitor\Integration;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Collection\Query as QueryValue;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Transfer\Output\Visitor\Query;

abstract class QueryTest extends VisitorTest
{
    public function setUp()
    {
        parent::setUp();

        $this->collectionService = $this->createCollectionService();
    }

    /**
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Implementation requires sub-visitor
     */
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor()
    {
        $this->getVisitor()->visit(new QueryValue());
    }

    public function getVisitor()
    {
        return new Query($this->collectionService);
    }

    public function acceptProvider()
    {
        return [
            [new QueryValue(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public function visitProvider()
    {
        return [
            [function () { return $this->collectionService->loadQuery(1); }, 'query/query_1.json'],
            [function () { return $this->collectionService->loadQuery(2); }, 'query/query_2.json'],
            [function () { return $this->collectionService->loadQuery(4); }, 'query/query_4.json'],
        ];
    }
}
