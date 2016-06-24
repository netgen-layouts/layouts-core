<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetType\RequestUriPrefix;

class RequestUriPrefixTest extends RequestUriTest
{
    public function setUp()
    {
        parent::setUp();

        $this->targetType = new RequestUriPrefix();
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUriPrefix::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('request_uri_prefix', $this->targetType->getIdentifier());
    }
}
