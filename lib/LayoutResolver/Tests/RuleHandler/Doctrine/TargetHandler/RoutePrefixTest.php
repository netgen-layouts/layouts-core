<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\RoutePrefix;
use PHPUnit_Framework_TestCase;

class RoutePrefixTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\RoutePrefix::getIdentifier
     */
    public function testGetIdentifier()
    {
        $targetHandler = new RoutePrefix();
        self::assertEquals('route_prefix', $targetHandler->getIdentifier());
    }
}
