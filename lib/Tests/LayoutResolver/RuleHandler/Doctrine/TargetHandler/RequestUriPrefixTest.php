<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\RequestUriPrefix;

class RequestUriPrefixTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\RequestUriPrefix::handleQuery
     */
    public function testLoadRequestUriPrefixRules()
    {
        $handler = $this->createHandler('request_uri_prefix', $this->getTargetHandler());

        $expected = array(
            array(
                'layout_id' => 7,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $handler->loadRules('request_uri_prefix', array('/the/answer?a=42')));
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new RequestUriPrefix();
    }
}
