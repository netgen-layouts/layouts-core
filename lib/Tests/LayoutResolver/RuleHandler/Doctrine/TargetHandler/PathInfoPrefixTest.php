<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\PathInfoPrefix;

class PathInfoPrefixTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\PathInfoPrefix::handleQuery
     */
    public function testLoadPathInfoPrefixRules()
    {
        $handler = $this->createHandler('path_info_prefix', $this->getTargetHandler());

        $expected = array(
            array(
                'layout_id' => 5,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $handler->loadRules('path_info_prefix', array('/the/answer')));
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new PathInfoPrefix();
    }
}
