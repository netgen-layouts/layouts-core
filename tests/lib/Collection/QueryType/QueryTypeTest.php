<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\QueryType;

use Netgen\BlockManager\Collection\QueryType\QueryType;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandler;
use PHPUnit\Framework\TestCase;

final class QueryTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Collection\QueryType\QueryType
     */
    private $queryType;

    public function setUp(): void
    {
        $this->queryType = new QueryType(
            [
                'handler' => new QueryTypeHandler(['val1', 'val2']),
                'type' => 'query_type',
                'isEnabled' => false,
                'name' => 'Query type',
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\QueryType::__construct
     * @covers \Netgen\BlockManager\Collection\QueryType\QueryType::getType
     */
    public function testGetType(): void
    {
        $this->assertEquals('query_type', $this->queryType->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\QueryType::isEnabled
     */
    public function testIsEnabled(): void
    {
        $this->assertFalse($this->queryType->isEnabled());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\QueryType::getName
     */
    public function testGetName(): void
    {
        $this->assertEquals('Query type', $this->queryType->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\QueryType::getValues
     */
    public function testGetValues(): void
    {
        $this->assertEquals(['val1', 'val2'], $this->queryType->getValues(new Query()));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\QueryType::getCount
     */
    public function testGetCount(): void
    {
        $this->assertEquals(2, $this->queryType->getCount(new Query()));
    }

    /**
     * @covers \Netgen\BlockManager\Collection\QueryType\QueryType::isContextual
     */
    public function testIsContextual(): void
    {
        $this->assertFalse($this->queryType->isContextual(new Query()));
    }
}
