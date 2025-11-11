<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Registry;

use ArrayIterator;
use Netgen\Layouts\Collection\Registry\QueryTypeRegistry;
use Netgen\Layouts\Exception\Collection\QueryTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Tests\Collection\Stubs\QueryType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(QueryTypeRegistry::class)]
final class QueryTypeRegistryTest extends TestCase
{
    private QueryType $queryType1;

    private QueryType $queryType2;

    private QueryTypeRegistry $registry;

    protected function setUp(): void
    {
        $this->queryType1 = new QueryType('query_type1');
        $this->queryType2 = new QueryType('query_type2', [], null, false, false);

        $this->registry = new QueryTypeRegistry(
            [
                'query_type1' => $this->queryType1,
                'query_type2' => $this->queryType2,
            ],
        );
    }

    public function testGetEnabledQueryTypes(): void
    {
        self::assertSame(
            [
                'query_type1' => $this->queryType1,
            ],
            $this->registry->getQueryTypes(true),
        );
    }

    public function testGetQueryTypes(): void
    {
        self::assertSame(
            [
                'query_type1' => $this->queryType1,
                'query_type2' => $this->queryType2,
            ],
            $this->registry->getQueryTypes(),
        );
    }

    public function testGetQueryType(): void
    {
        self::assertSame($this->queryType1, $this->registry->getQueryType('query_type1'));
    }

    public function testGetQueryTypeThrowsQueryTypeException(): void
    {
        $this->expectException(QueryTypeException::class);
        $this->expectExceptionMessage('Query type with "other_query_type" identifier does not exist.');

        $this->registry->getQueryType('other_query_type');
    }

    public function testHasQueryType(): void
    {
        self::assertTrue($this->registry->hasQueryType('query_type1'));
    }

    public function testHasQueryTypeWithNoQueryType(): void
    {
        self::assertFalse($this->registry->hasQueryType('other_query_type'));
    }

    public function testGetIterator(): void
    {
        self::assertInstanceOf(ArrayIterator::class, $this->registry->getIterator());

        $queryTypes = [];
        foreach ($this->registry as $identifier => $queryType) {
            $queryTypes[$identifier] = $queryType;
        }

        self::assertSame($this->registry->getQueryTypes(), $queryTypes);
    }

    public function testCount(): void
    {
        self::assertCount(2, $this->registry);
    }

    public function testOffsetExists(): void
    {
        self::assertArrayHasKey('query_type1', $this->registry);
        self::assertArrayNotHasKey('other', $this->registry);
    }

    public function testOffsetGet(): void
    {
        self::assertSame($this->queryType1, $this->registry['query_type1']);
    }

    public function testOffsetSet(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        $this->registry['query_type1'] = $this->queryType1;
    }

    public function testOffsetUnset(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Method call not supported.');

        unset($this->registry['query_type1']);
    }
}
