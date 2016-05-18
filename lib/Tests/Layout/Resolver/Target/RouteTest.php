<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Target;

use Netgen\BlockManager\Layout\Resolver\Target\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Target\Route::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new Route();
        self::assertEquals('route', $target->getIdentifier());
    }
}
