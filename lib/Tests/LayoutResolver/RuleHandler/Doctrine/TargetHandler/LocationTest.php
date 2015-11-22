<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Location;

class LocationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Location::getTargetIdentifier
     */
    public function testGetTargetIdentifier()
    {
        $targetHandler = new Location();
        self::assertEquals('location', $targetHandler->getTargetIdentifier());
    }
}
