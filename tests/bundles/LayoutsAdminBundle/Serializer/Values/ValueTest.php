<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Serializer\Values;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\Tests\API\Stubs\Value as StubValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(Value::class)]
final class ValueTest extends TestCase
{
    private Value $value;

    private StubValue $innerValue;

    protected function setUp(): void
    {
        $this->innerValue = new StubValue();

        $this->value = new Value($this->innerValue, Response::HTTP_ACCEPTED);
    }

    public function testGetValue(): void
    {
        self::assertSame($this->innerValue, $this->value->getValue());
    }

    public function testGetStatusCode(): void
    {
        self::assertSame(Response::HTTP_ACCEPTED, $this->value->getStatusCode());
    }
}
