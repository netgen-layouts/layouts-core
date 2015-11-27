<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\Target;

use Netgen\BlockManager\LayoutResolver\Target\RequestUriPrefix;

class RequestUriPrefixTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Target\RequestUriPrefix::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new RequestUriPrefix();
        self::assertEquals('request_uri_prefix', $target->getIdentifier());
    }
}
