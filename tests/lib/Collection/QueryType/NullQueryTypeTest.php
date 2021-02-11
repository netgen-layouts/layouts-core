<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\QueryType;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\QueryType\NullQueryType;
use PHPUnit\Framework\TestCase;

final class NullQueryTypeTest extends TestCase
{
    private NullQueryType $queryType;

    protected function setUp(): void
    {
        $this->queryType = new NullQueryType('type');
    }

    /**
     * @covers \Netgen\Layouts\Collection\QueryType\NullQueryType::__construct
     * @covers \Netgen\Layouts\Collection\QueryType\NullQueryType::getType
     */
    public function testGetType(): void
    {
        self::assertSame('type', $this->queryType->getType());
    }

    /**
     * @covers \Netgen\Layouts\Collection\QueryType\NullQueryType::isEnabled
     */
    public function testIsEnabled(): void
    {
        self::assertTrue($this->queryType->isEnabled());
    }

    /**
     * @covers \Netgen\Layouts\Collection\QueryType\NullQueryType::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Invalid query type', $this->queryType->getName());
    }

    /**
     * @covers \Netgen\Layouts\Collection\QueryType\NullQueryType::getValues
     */
    public function testGetValues(): void
    {
        $values = $this->queryType->getValues(new Query());

        self::assertIsArray($values);
        self::assertEmpty($values);
    }

    /**
     * @covers \Netgen\Layouts\Collection\QueryType\NullQueryType::getCount
     */
    public function testGetCount(): void
    {
        self::assertSame(0, $this->queryType->getCount(new Query()));
    }

    /**
     * @covers \Netgen\Layouts\Collection\QueryType\NullQueryType::isContextual
     */
    public function testIsContextual(): void
    {
        self::assertFalse($this->queryType->isContextual(new Query()));
    }
}
