<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\PathInfo;

class PathInfoTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\PathInfo::getTargetIdentifier
     */
    public function testGetTargetIdentifier()
    {
        $targetHandler = $this->getTargetHandler();
        self::assertEquals('path_info', $targetHandler->getTargetIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\PathInfo::handleQuery
     */
    public function testLoadPathInfoRules()
    {
        $handler = $this->createHandler();

        $expected = array(
            array(
                'layout_id' => 4,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $handler->loadRules('path_info', array('/the/answer')));
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
