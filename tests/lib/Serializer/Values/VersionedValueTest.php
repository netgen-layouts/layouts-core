<?php

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

    public function setUp()
    {
        $this->value = new VersionedValue(new Value(), 42, Response::HTTP_ACCEPTED);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Values\AbstractVersionedValue::__construct
     * @covers \Netgen\BlockManager\Serializer\Values\VersionedValue::getVersion
     */
    public function testGetVersion()
    {
        $this->assertEquals(42, $this->value->getVersion());
    }
}
