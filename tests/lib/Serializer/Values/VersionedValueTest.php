<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Serializer\Values;

use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Tests\API\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class VersionedValueTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Serializer\Values\VersionedValue
     */
    private $value;

    public function setUp(): void
    {
        $this->value = new VersionedValue(new Value(), 42, Response::HTTP_ACCEPTED);
    }

    /**
     * @covers \Netgen\Layouts\Serializer\Values\VersionedValue::__construct
     * @covers \Netgen\Layouts\Serializer\Values\VersionedValue::getVersion
     */
    public function testGetVersion(): void
    {
        self::assertSame(42, $this->value->getVersion());
    }
}
