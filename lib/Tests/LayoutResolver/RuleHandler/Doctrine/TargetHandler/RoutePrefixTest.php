<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\RoutePrefix;

class RoutePrefixTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\RoutePrefix::getIdentifier
     */
    public function testGetTargetIdentifier()
    {
        $targetHandler = new RoutePrefix();
        self::assertEquals('route_prefix', $targetHandler->getTargetIdentifier());
    }
}
