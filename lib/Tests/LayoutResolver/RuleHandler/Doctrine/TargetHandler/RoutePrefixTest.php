<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\RoutePrefix;

class RoutePrefixTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\RoutePrefix::getTargetIdentifier
     */
    public function testGetTargetIdentifier()
    {
        $targetHandler = $this->getTargetHandler();
        self::assertEquals('route_prefix', $targetHandler->getTargetIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\RoutePrefix::handleQuery
     */
    public function testLoadRoutePrefixRules()
    {
        $handler = $this->createHandler();

        $expected = array(
            4 => array(
                'layout_id' => 1,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $handler->loadRules('route_prefix', array('my_cool_route')));
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new RoutePrefix();
    }
}
