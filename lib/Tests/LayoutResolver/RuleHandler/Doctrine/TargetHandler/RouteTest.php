<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Route::getTargetIdentifier
     */
    public function testGetTargetIdentifier()
    {
        $targetHandler = new Route();
        self::assertEquals('route', $targetHandler->getTargetIdentifier());
    }
}
