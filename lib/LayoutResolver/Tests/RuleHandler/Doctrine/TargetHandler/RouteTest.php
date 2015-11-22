<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Route::getIdentifier
     */
    public function testGetIdentifier()
    {
        $targetHandler = new Route();
        self::assertEquals('route', $targetHandler->getIdentifier());
    }
}
