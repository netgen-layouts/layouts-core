<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result;

use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Collection\Result\ResultSet;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ResultSet::class)]
final class ResultSetTest extends TestCase
{
    public function testObject(): void
    {
        $collection = new Collection();

        $resultItem = new Result(0, new CmsItem());

        $result = ResultSet::fromArray(
            [
                'collection' => $collection,
                'results' => [$resultItem],
                'totalCount' => 15,
                'offset' => 3,
                'limit' => 5,
            ],
        );

        self::assertSame($collection, $result->getCollection());
        self::assertSame([$resultItem], $result->getResults());
        self::assertFalse($result->isContextual());
        self::assertSame(15, $result->getTotalCount());
        self::assertSame(3, $result->getOffset());
        self::assertSame(5, $result->getLimit());

        self::assertSame([$resultItem], [...$result]);

        self::assertCount(1, $result);

        self::assertTrue(isset($result[0]));
        self::assertSame($resultItem, $result[0]);
    }

    public function testSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $result = ResultSet::fromArray(
            [
                'results' => [new Result(0, new CmsItem())],
            ],
        );

        $result[0] = new Result(0, new CmsItem());
    }

    public function testUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $result = ResultSet::fromArray(
            [
                'results' => [new Result(0, new CmsItem())],
            ],
        );

        unset($result[0]);
    }

    public function testIsDynamic(): void
    {
        $result = ResultSet::fromArray(
            [
                'collection' => Collection::fromArray(
                    [
                        'query' => new Query(),
                    ],
                ),
            ],
        );

        self::assertTrue($result->isDynamic());
    }

    public function testIsDynamicWithManualCollection(): void
    {
        $result = ResultSet::fromArray(
            [
                'collection' => new Collection(),
            ],
        );

        self::assertFalse($result->isDynamic());
    }

    public function testIsContextual(): void
    {
        $result = ResultSet::fromArray(
            [
                'collection' => Collection::fromArray(
                    [
                        'query' => Query::fromArray(
                            [
                                'queryType' => new QueryType('type', [], null, false),
                            ],
                        ),
                    ],
                ),
            ],
        );

        self::assertFalse($result->isContextual());
    }

    public function testIsContextualWithManualCollection(): void
    {
        $result = ResultSet::fromArray(
            [
                'collection' => new Collection(),
            ],
        );

        self::assertFalse($result->isContextual());
    }

    public function testIsContextualWithContextualQuery(): void
    {
        $result = ResultSet::fromArray(
            [
                'collection' => Collection::fromArray(
                    [
                        'query' => Query::fromArray(
                            [
                                'queryType' => new QueryType('type', [], null, true),
                            ],
                        ),
                    ],
                ),
            ],
        );

        self::assertTrue($result->isContextual());
    }
}
