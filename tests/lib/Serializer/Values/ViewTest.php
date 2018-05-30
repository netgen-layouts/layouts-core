<?php

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class ViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Values\View
     */
    private $value;

    public function setUp()
    {
        $this->value = new View(new Value(), 42, Response::HTTP_ACCEPTED);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Values\View::__construct
     * @covers \Netgen\BlockManager\Serializer\Values\View::getVersion
     */
    public function testGetVersion()
    {
        $this->assertEquals(42, $this->value->getVersion());
    }
}
