<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Target;

use Netgen\BlockManager\Layout\Resolver\Target\RequestUriPrefix;

class RequestUriPrefixTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Target\RequestUriPrefix::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new RequestUriPrefix();
        self::assertEquals('request_uri_prefix', $target->getIdentifier());
    }
}
