<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\Target;

use Netgen\BlockManager\LayoutResolver\Target\RoutePrefix;

class RoutePrefixTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Target\RoutePrefix::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new RoutePrefix();
        self::assertEquals('route_prefix', $target->getIdentifier());
    }
}
