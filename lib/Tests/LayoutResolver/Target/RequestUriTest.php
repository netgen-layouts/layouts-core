<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\Target;

use Netgen\BlockManager\LayoutResolver\Target\RequestUri;

class RequestUriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Target\RequestUri::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new RequestUri();
        self::assertEquals('request_uri', $target->getIdentifier());
    }
}
