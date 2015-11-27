<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\RequestUri;

class RequestUriTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\RequestUri::handleQuery
     */
    public function testLoadRequestUriRules()
    {
        $handler = $this->createHandler('request_uri', $this->getTargetHandler());

        $expected = array(
            array(
                'layout_id' => 6,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $handler->loadRules('request_uri', array('/the/answer?a=42')));
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new RequestUri();
    }
}
