<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetType\PathInfoPrefix;

class PathInfoPrefixTest extends PathInfoTest
{
    public function setUp()
    {
        parent::setUp();

        $this->targetType = new PathInfoPrefix();
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\PathInfoPrefix::getType
     */
    public function testGetType()
    {
        $this->assertEquals('path_info_prefix', $this->targetType->getType());
    }
}
