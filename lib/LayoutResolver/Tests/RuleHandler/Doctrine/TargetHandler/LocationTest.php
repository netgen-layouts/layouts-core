<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Location;
use PHPUnit_Framework_TestCase;

class LocationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Location::getIdentifier
     */
    public function testGetIdentifier()
    {
        $targetHandler = new Location();
        self::assertEquals('location', $targetHandler->getIdentifier());
    }
}
