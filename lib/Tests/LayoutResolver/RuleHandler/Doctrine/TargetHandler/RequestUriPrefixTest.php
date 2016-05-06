<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\RequestUriPrefix;

class RequestUriPrefixTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\RequestUriPrefix::handleQuery
     */
    public function testLoadRules()
    {
        $expected = array(
            array(
                'layout_id' => 7,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $this->handler->loadRules($this->getTargetIdentifier(), array('/the/answer?a=42')));
    }

    /**
     * Returns the target handler identifier under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetIdentifier()
    {
        return 'request_uri_prefix';
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
