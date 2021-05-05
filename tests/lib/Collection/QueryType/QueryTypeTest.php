<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\QueryType;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\QueryType\QueryType;
use Netgen\Layouts\Tests\Collection\Stubs\QueryTypeHandler;
use PHPUnit\Framework\TestCase;
use stdClass;

final class QueryTypeTest extends TestCase
{
    /**
     * @var object[]
     */
    private array $values;

    private QueryType $queryType;

    protected function setUp(): void
    {
        $this->values = [new stdClass(), new stdClass()];

        $this->queryType = QueryType::fromArray(
            [
                'handler' => new QueryTypeHandler($this->values),
                'type' => 'query_type',
                'isEnabled' => false,
                'name' => 'Query type',
            ],
        );
    }

    /**
     * @covers \Netgen\Layouts\Collection\QueryType\QueryType::getType
     */
    public function testGetType(): void
    {
        self::assertSame('query_type', $this->queryType->getType());
    }

    /**
     * @covers \Netgen\Layouts\Collection\QueryType\QueryType::isEnabled
     */
    public function testIsEnabled(): void
    {
        self::assertFalse($this->queryType->isEnabled());
    }

    /**
     * @covers \Netgen\Layouts\Collection\QueryType\QueryType::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Query type', $this->queryType->getName());
    }

    /**
     * @covers \Netgen\Layouts\Collection\QueryType\QueryType::getValues
     */
    public function testGetValues(): void
    {
        self::assertSame($this->values, $this->queryType->getValues(new Query()));
    }

    /**
     * @covers \Netgen\Layouts\Collection\QueryType\QueryType::getCount
     */
    public function testGetCount(): void
    {
        self::assertSame(2, $this->queryType->getCount(new Query()));
    }

    /**
     * @covers \Netgen\Layouts\Collection\QueryType\QueryType::isContextual
     */
    public function testIsContextual(): void
    {
        self::assertFalse($this->queryType->isContextual(new Query()));
    }
}
