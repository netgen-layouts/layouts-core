<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Transfer\Output\Visitor\QueryVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Ramsey\Uuid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\Collection\Query>
 */
abstract class QueryVisitorTestBase extends VisitorTestBase
{
    public function getVisitor(): VisitorInterface
    {
        return new QueryVisitor($this->collectionService);
    }

    public static function acceptDataProvider(): iterable
    {
        return [
            [new Query(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    public static function visitDataProvider(): iterable
    {
        return [
            [fn (): Query => $this->collectionService->loadQuery(Uuid::fromString('86c5af5d-bcb3-5a93-aeed-754466d76878')), 'query/query_1.json'],
            [fn (): Query => $this->collectionService->loadQuery(Uuid::fromString('0303abc4-c894-59b5-ba95-5cf330b99c66')), 'query/query_2.json'],
            [fn (): Query => $this->collectionService->loadQuery(Uuid::fromString('6d60fcbc-ae38-57c2-af72-e462a3e5c9f2')), 'query/query_4.json'],
        ];
    }
}
