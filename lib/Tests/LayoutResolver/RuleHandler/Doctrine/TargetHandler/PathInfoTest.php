<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\PathInfo;

class PathInfoTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\PathInfo::handleQuery
     */
    public function testLoadRules()
    {
        $expected = array(
            array(
                'layout_id' => 4,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $this->handler->loadRules($this->getTargetIdentifier(), array('/the/answer')));
    }

    /**
     * Returns the target handler identifier under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetIdentifier()
    {
        return 'path_info';
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new PathInfo();
    }
}
