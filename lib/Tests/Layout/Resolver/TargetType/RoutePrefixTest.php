<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetType\RoutePrefix;

class RoutePrefixTest extends RouteTest
{
    public function setUp()
    {
        parent::setUp();

        $this->targetType = new RoutePrefix();
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\RoutePrefix::getType
     */
    public function testGetType()
    {
        $this->assertEquals('route_prefix', $this->targetType->getType());
    }
}
