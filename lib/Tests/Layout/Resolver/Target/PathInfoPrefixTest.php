<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Target;

use Netgen\BlockManager\Layout\Resolver\Target\PathInfoPrefix;

class PathInfoPrefixTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Target\PathInfoPrefix::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new PathInfoPrefix();
        self::assertEquals('path_info_prefix', $target->getIdentifier());
    }
}
