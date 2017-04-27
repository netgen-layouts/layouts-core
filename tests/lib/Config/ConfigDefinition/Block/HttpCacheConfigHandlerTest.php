<?php

namespace Netgen\BlockManager\Tests\Config\ConfigDefinition\Block;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Config\ConfigDefinition\Block\HttpCacheConfigHandler;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;
use PHPUnit\Framework\TestCase;

class HttpCacheConfigHandlerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheableResolverMock;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinition\ConfigDefinitionHandlerInterface
     */
    protected $handler;

    public function setUp()
    {
        $this->cacheableResolverMock = $this->createMock(CacheableResolverInterface::class);

        $this->handler = new HttpCacheConfigHandler(
            $this->cacheableResolverMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Config\ConfigDefinition\Block\HttpCacheConfigHandler::isEnabled
     */
    public function testIsEnabled()
    {
        $this->cacheableResolverMock
            ->expects($this->any())
            ->method('isCacheable')
            ->with($this->equalTo(new Block()))
            ->will($this->returnValue(true));

        $this->assertTrue($this->handler->isEnabled(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\Config\ConfigDefinition\Block\HttpCacheConfigHandler::isEnabled
     */
    public function testIsEnabledWithNoBlock()
    {
        $this->cacheableResolverMock
            ->expects($this->never())
            ->method('isCacheable');

        $this->assertFalse($this->handler->isEnabled($this->createMock(ConfigAwareValue::class)));
    }
}
