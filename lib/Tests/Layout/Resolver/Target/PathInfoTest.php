<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Target;

use Netgen\BlockManager\Layout\Resolver\Target\PathInfo;

class PathInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Target\PathInfo::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new PathInfo();
        self::assertEquals('path_info', $target->getIdentifier());
    }
}
