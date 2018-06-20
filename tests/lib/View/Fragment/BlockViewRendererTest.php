<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Fragment;

use Netgen\BlockManager\Context\ContextInterface;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Fragment\BlockViewRenderer;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\View\View\LayoutView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

final class BlockViewRendererTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contextMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $cacheableResolverMock;

    /**
     * @var \Netgen\BlockManager\View\Fragment\BlockViewRenderer
     */
    private $blockViewRenderer;

    public function setUp(): void
    {
        $this->contextMock = $this->createMock(ContextInterface::class);
        $this->cacheableResolverMock = $this->createMock(CacheableResolverInterface::class);

        $this->blockViewRenderer = new BlockViewRenderer(
            $this->contextMock,
            $this->cacheableResolverMock,
            'block_controller',
            ['default', 'api']
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Fragment\BlockViewRenderer::__construct
     * @covers \Netgen\BlockManager\View\Fragment\BlockViewRenderer::supportsView
     */
    public function testSupportsView(): void
    {
        $view = new BlockView(new Block());
        $view->setContext('default');

        $this->cacheableResolverMock
            ->expects($this->any())
            ->method('isCacheable')
            ->with($this->equalTo(new Block()))
            ->will($this->returnValue(true));

        $this->assertTrue($this->blockViewRenderer->supportsView($view));
    }

    /**
     * @covers \Netgen\BlockManager\View\Fragment\BlockViewRenderer::supportsView
     */
    public function testSupportsViewWithNoBlockView(): void
    {
        $view = new LayoutView(new Layout());

        $this->cacheableResolverMock
            ->expects($this->never())
            ->method('isCacheable');

        $this->assertFalse($this->blockViewRenderer->supportsView($view));
    }

    /**
     * @covers \Netgen\BlockManager\View\Fragment\BlockViewRenderer::supportsView
     */
    public function testSupportsViewWithNonSupportedContext(): void
    {
        $view = new BlockView(new Block());
        $view->setContext('unsupported');

        $this->cacheableResolverMock
            ->expects($this->never())
            ->method('isCacheable');

        $this->assertFalse($this->blockViewRenderer->supportsView($view));
    }

    /**
     * @covers \Netgen\BlockManager\View\Fragment\BlockViewRenderer::getController
     */
    public function testGetController(): void
    {
        $this->contextMock->expects($this->once())
            ->method('all')
            ->will($this->returnValue(['var' => 'value']));

        $block = new Block(
            [
                'id' => 42,
                'availableLocales' => ['en'],
                'locale' => 'en',
            ]
        );

        $view = new BlockView($block);
        $view->setContext('default');

        $controller = $this->blockViewRenderer->getController($view);

        $this->assertInstanceOf(ControllerReference::class, $controller);
        $this->assertSame('block_controller', $controller->controller);

        $this->assertSame(
            [
                'blockId' => 42,
                'locale' => 'en',
                'viewContext' => 'default',
                'ngbmContext' => ['var' => 'value'],
                '_ngbm_status' => 'published',
            ],
            $controller->attributes
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Fragment\BlockViewRenderer::getController
     */
    public function testGetControllerWithInvalidView(): void
    {
        $this->assertNull($this->blockViewRenderer->getController(new View(new Value())));
    }
}
