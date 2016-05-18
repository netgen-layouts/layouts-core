<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Target;

use Netgen\BlockManager\Layout\Resolver\Target\RoutePrefix;

class RoutePrefixTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Target\RoutePrefix::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new RoutePrefix();
        self::assertEquals('route_prefix', $target->getIdentifier());
    }
}
