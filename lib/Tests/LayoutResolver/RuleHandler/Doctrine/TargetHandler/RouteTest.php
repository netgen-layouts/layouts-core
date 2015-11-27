<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Route;

class RouteTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Route::handleQuery
     */
    public function testLoadRouteRules()
    {
        $handler = $this->createHandler('route', $this->getTargetHandler());

        $expected = array(
            array(
                'layout_id' => 1,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $handler->loadRules('route', array('my_cool_route')));
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new Route();
    }
}
