<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class VersionedValueTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    private $value;

    public function setUp(): void
    {
        $this->value = new VersionedValue(new Value(), 42, Response::HTTP_ACCEPTED);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Values\VersionedValue::__construct
     * @covers \Netgen\BlockManager\Serializer\Values\VersionedValue::getVersion
     */
    public function testGetVersion(): void
    {
        $this->assertSame(42, $this->value->getVersion());
    }
}
