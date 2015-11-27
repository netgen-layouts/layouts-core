<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\Target;

use Netgen\BlockManager\LayoutResolver\Target\PathInfoPrefix;

class PathInfoPrefixTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Target\PathInfoPrefix::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new PathInfoPrefix();
        self::assertEquals('path_info_prefix', $target->getIdentifier());
    }
}
