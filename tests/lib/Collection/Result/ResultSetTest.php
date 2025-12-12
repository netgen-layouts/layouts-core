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
        $collection = Collection::fromArray(['query' => null]);

        $resultItem = Result::fromArray(['position' => 0, 'item' => new CmsItem()]);

        $result = ResultSet::fromArray(
            [
                'collection' => $collection,
                'results' => [$resultItem],
                'totalCount' => 15,
                'offset' => 3,
                'limit' => 5,
            ],
        );

        self::assertSame($collection, $result->collection);
        self::assertSame([$resultItem], $result->results);
        self::assertFalse($result->isContextual);
        self::assertSame(15, $result->totalCount);
        self::assertSame(3, $result->offset);
        self::assertSame(5, $result->limit);

        self::assertSame([$resultItem], [...$result]);

        self::assertCount(1, $result);

        self::assertArrayHasKey(0, $result);
        self::assertSame($resultItem, $result[0]);
    }

    public function testSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $result = ResultSet::fromArray(
            [
                'results' => [Result::fromArray(['position' => 0, 'item' => new CmsItem()])],
            ],
        );

        $result->offsetSet(0, Result::fromArray(['position' => 0, 'item' => new CmsItem()]));
    }

    public function testUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $result = ResultSet::fromArray(
            [
                'results' => [Result::fromArray(['position' => 0, 'item' => new CmsItem()])],
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

        self::assertTrue($result->isDynamic);
    }

    public function testIsDynamicWithManualCollection(): void
    {
        $result = ResultSet::fromArray(
            [
                'collection' => Collection::fromArray(['query' => null]),
            ],
        );

        self::assertFalse($result->isDynamic);
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

        self::assertFalse($result->isContextual);
    }

    public function testIsContextualWithManualCollection(): void
    {
        $result = ResultSet::fromArray(
            [
                'collection' => Collection::fromArray(['query' => null]),
            ],
        );

        self::assertFalse($result->isContextual);
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

        self::assertTrue($result->isContextual);
    }
}
