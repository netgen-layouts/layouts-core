<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Transfer\Output\Visitor\QueryVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Ramsey\Uuid\Uuid;

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
            [function (): Query { return $this->collectionService->loadQuery(Uuid::fromString('86c5af5d-bcb3-5a93-aeed-754466d76878')); }, 'query/query_1.json'],
            [function (): Query { return $this->collectionService->loadQuery(Uuid::fromString('0303abc4-c894-59b5-ba95-5cf330b99c66')); }, 'query/query_2.json'],
            [function (): Query { return $this->collectionService->loadQuery(Uuid::fromString('6d60fcbc-ae38-57c2-af72-e462a3e5c9f2')); }, 'query/query_4.json'],
        ];
    }
}
