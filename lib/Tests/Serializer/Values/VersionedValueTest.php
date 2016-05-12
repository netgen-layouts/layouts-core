<?php

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Symfony\Component\HttpFoundation\Response;

class VersionedValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    protected $value;

    public function setUp()
    {
        $this->value = new VersionedValue(new Value(), 42, Response::HTTP_ACCEPTED);
    }

    /**
     * @covers Netgen\BlockManager\Serializer\Values\VersionedValue::__construct
     * @covers Netgen\BlockManager\Serializer\Values\VersionedValue::getVersion
     */
    public function testGetVersion()
    {
        self::assertEquals(42, $this->value->getVersion());
    }
}
