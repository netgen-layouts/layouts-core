<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\Target;

use Netgen\BlockManager\LayoutResolver\Target\PathInfo;

class PathInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Target\PathInfo::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new PathInfo();
        self::assertEquals('path_info', $target->getIdentifier());
    }
}
