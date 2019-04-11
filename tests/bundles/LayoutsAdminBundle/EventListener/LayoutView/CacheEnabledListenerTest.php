<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener\LayoutView;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView\CacheEnabledListener;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\HttpCache\ClientInterface;
use Netgen\Layouts\HttpCache\NullClient;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\View\LayoutView;
use Netgen\Layouts\View\ViewInterface;
use PHPUnit\Framework\TestCase;

final class CacheEnabledListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView\CacheEnabledListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->listener = new CacheEnabledListener(new NullClient());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView\CacheEnabledListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [sprintf('%s.%s', LayoutsEvents::BUILD_VIEW, 'layout') => 'onBuildView'],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView\CacheEnabledListener::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView\CacheEnabledListener::onBuildView
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
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView\CacheEnabledListener::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView\CacheEnabledListener::onBuildView
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
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView\CacheEnabledListener::onBuildView
     */
    public function testOnBuildViewWithNoLayoutView(): void
    {
        $view = new View(new Value());
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        self::assertSame([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView\CacheEnabledListener::onBuildView
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
