<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Target;

use Netgen\BlockManager\Layout\Resolver\Target\RequestUri;

class RequestUriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Target\RequestUri::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new RequestUri();
        self::assertEquals('request_uri', $target->getIdentifier());
    }
}
