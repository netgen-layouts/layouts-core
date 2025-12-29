<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Transfer\Output\Visitor\QueryVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends \Netgen\Layouts\Tests\Transfer\Output\Visitor\Integration\VisitorTestBase<\Netgen\Layouts\API\Values\Collection\Query>
 */
abstract class QueryVisitorTestBase extends VisitorTestBase
{
    final public static function acceptDataProvider(): iterable
    {
        return [
            [new Query(), true],
            [new Layout(), false],
            [new Block(), false],
        ];
    }

    final public static function visitDataProvider(): iterable
    {
        return [
            ['query/query_1.json', '86c5af5d-bcb3-5a93-aeed-754466d76878'],
            ['query/query_2.json', '0303abc4-c894-59b5-ba95-5cf330b99c66'],
            ['query/query_4.json', '6d60fcbc-ae38-57c2-af72-e462a3e5c9f2'],
        ];
    }

    final protected function getVisitor(): VisitorInterface
    {
        return new QueryVisitor($this->collectionService);
    }

    final protected function loadValue(string $id, string ...$additionalParameters): Query
    {
        return $this->collectionService->loadQuery(Uuid::fromString($id));
    }
}
