<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Serializer\Values;

use Netgen\Layouts\Serializer\Values\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class ValueTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Serializer\Values\Value
     */
    private $value;

    /**
     * @var \Netgen\Layouts\Tests\API\Stubs\Value
     */
    private $innerValue;

    protected function setUp(): void
    {
        $this->value = new Value($this->innerValue, Response::HTTP_ACCEPTED);
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Values\Value::__construct
     * @covers \Netgen\Layouts\Serializer\Values\Value::getValue
     */
    public function testGetValue(): void
    {
        self::assertSame($this->innerValue, $this->value->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Values\Value::__construct
     * @covers \Netgen\Layouts\Serializer\Values\Value::getStatusCode
     */
    public function testGetStatusCode(): void
    {
        self::assertSame(Response::HTTP_ACCEPTED, $this->value->getStatusCode());
    }
}
