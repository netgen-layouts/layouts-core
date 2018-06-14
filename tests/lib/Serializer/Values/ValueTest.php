<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Tests\Core\Stubs\Value as StubValue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class ValueTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Values\Value
     */
    private $value;

    public function setUp(): void
    {
        $this->value = new Value(new StubValue(), Response::HTTP_ACCEPTED);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Values\Value::__construct
     * @covers \Netgen\BlockManager\Serializer\Values\Value::getValue
     */
    public function testGetValue(): void
    {
        $this->assertEquals(new StubValue(), $this->value->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Values\Value::__construct
     * @covers \Netgen\BlockManager\Serializer\Values\Value::getStatusCode
     */
    public function testGetStatusCode(): void
    {
        $this->assertEquals(Response::HTTP_ACCEPTED, $this->value->getStatusCode());
    }
}
