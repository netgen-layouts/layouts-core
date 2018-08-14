<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class ValueTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Values\Value
     */
    private $value;

    /**
     * @var \Netgen\BlockManager\Tests\API\Stubs\Value
     */
    private $innerValue;

    public function setUp(): void
    {
        $this->value = new Value($this->innerValue, Response::HTTP_ACCEPTED);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Values\Value::__construct
     * @covers \Netgen\BlockManager\Serializer\Values\Value::getValue
     */
    public function testGetValue(): void
    {
        self::assertSame($this->innerValue, $this->value->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Values\Value::__construct
     * @covers \Netgen\BlockManager\Serializer\Values\Value::getStatusCode
     */
    public function testGetStatusCode(): void
    {
        self::assertSame(Response::HTTP_ACCEPTED, $this->value->getStatusCode());
    }
}
