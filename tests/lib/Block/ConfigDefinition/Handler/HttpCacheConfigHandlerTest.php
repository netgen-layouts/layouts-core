<?php

namespace Netgen\BlockManager\Tests\Block\ConfigDefinition\Handler;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Block\ConfigDefinition\Handler\HttpCacheConfigHandler;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;
use PHPUnit\Framework\TestCase;

final class HttpCacheConfigHandlerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $cacheableResolverMock;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface
     */
    private $handler;

    public function setUp()
    {
        $this->cacheableResolverMock = $this->createMock(CacheableResolverInterface::class);

        $this->handler = new HttpCacheConfigHandler(
            $this->cacheableResolverMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\ConfigDefinition\Handler\HttpCacheConfigHandler::__construct
     * @covers \Netgen\BlockManager\Block\ConfigDefinition\Handler\HttpCacheConfigHandler::isEnabled
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
     * @covers \Netgen\BlockManager\Block\ConfigDefinition\Handler\HttpCacheConfigHandler::isEnabled
     */
    public function testIsEnabledWithNoBlock()
    {
        $this->cacheableResolverMock
            ->expects($this->never())
            ->method('isCacheable');

        $this->assertFalse($this->handler->isEnabled($this->createMock(ConfigAwareValue::class)));
    }
}
