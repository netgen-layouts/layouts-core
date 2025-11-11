<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\QueryType;

use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\QueryType\NullQueryType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NullQueryType::class)]
final class NullQueryTypeTest extends TestCase
{
    private NullQueryType $queryType;

    protected function setUp(): void
    {
        $this->queryType = new NullQueryType('type');
    }

    public function testGetType(): void
    {
        self::assertSame('type', $this->queryType->getType());
    }

    public function testGetName(): void
    {
        self::assertSame('Invalid query type', $this->queryType->getName());
    }

    public function testGetValues(): void
    {
        $values = $this->queryType->getValues(new Query());

        self::assertIsArray($values);
        self::assertEmpty($values);
    }

    public function testGetCount(): void
    {
        self::assertSame(0, $this->queryType->getCount(new Query()));
    }

    public function testIsContextual(): void
    {
        self::assertFalse($this->queryType->isContextual(new Query()));
    }
}
