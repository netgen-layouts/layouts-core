<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\EventListener\LayoutView;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\HttpCache\ClientInterface;
use Netgen\BlockManager\HttpCache\NullClient;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\View\LayoutView;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\CacheEnabledListener;
use PHPUnit\Framework\TestCase;

final class CacheEnabledListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\CacheEnabledListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->listener = new CacheEnabledListener(new NullClient());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\CacheEnabledListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [sprintf('%s.%s', BlockManagerEvents::BUILD_VIEW, 'layout') => 'onBuildView'],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\CacheEnabledListener::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\CacheEnabledListener::onBuildView
     */
    public function testOnBuildView(): void
    {
        $view = new LayoutView(new Layout());
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        self::assertSame(
            [
                'http_cache_enabled' => false,
            ],
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\CacheEnabledListener::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\CacheEnabledListener::onBuildView
     */
    public function testOnBuildViewWithNoNullClient(): void
    {
        $this->listener = new CacheEnabledListener($this->createMock(ClientInterface::class));

        $view = new LayoutView(new Layout());
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        self::assertSame(
            [
                'http_cache_enabled' => true,
            ],
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\CacheEnabledListener::onBuildView
     */
    public function testOnBuildViewWithNoLayoutView(): void
    {
        $view = new View(new Value());
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        self::assertSame([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView\CacheEnabledListener::onBuildView
     */
    public function testOnBuildViewWithWrongContext(): void
    {
        $view = new LayoutView(new Layout());
        $view->setContext(ViewInterface::CONTEXT_API);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        self::assertSame([], $event->getParameters());
    }
}
