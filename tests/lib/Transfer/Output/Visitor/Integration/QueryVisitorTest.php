<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Transfer\Output\Visitor\QueryVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

abstract class QueryVisitorTest extends VisitorTest
{
    public function testVisitThrowsRuntimeExceptionWithoutSubVisitor(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Implementation requires sub-visitor');

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
