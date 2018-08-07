<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\BlockView;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class GetCollectionPagerListenerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $resultBuilderMock;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->resultBuilderMock = $this->createMock(ResultBuilderInterface::class);
        $this->requestStack = new RequestStack();

        $this->listener = new GetCollectionPagerListener(
            new PagerFactory(
                $this->resultBuilderMock,
                200
            ),
            $this->requestStack,
            [ViewInterface::CONTEXT_AJAX]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [sprintf('%s.%s', BlockManagerEvents::RENDER_VIEW, 'block') => 'onRenderView'],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::getMaxPages
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderView(): void
    {
        $request = Request::create('/');
        $request->query->set('page', 3);

        $this->requestStack->push($request);

        $collection = Collection::fromArray(['offset' => 3, 'limit' => 5, 'query' => new Query()]);

        $view = new BlockView(
            Block::fromArray(
                [
                    'definition' => new BlockDefinition(),
                    'collections' => new ArrayCollection(
                        [
                            'default' => $collection,
                        ]
                    ),
                ]
            )
        );

        $view->addParameter('collection_identifier', 'default');

        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects(self::at(0))
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(0),
                self::identicalTo(0),
                self::identicalTo(0)
            )
            ->will(self::returnValue(ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])));

        $this->resultBuilderMock
            ->expects(self::at(1))
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(13),
                self::identicalTo(5),
                self::identicalTo(0)
            )
            ->will(self::returnValue(ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])));

        $this->listener->onRenderView($event);

        $result = $event->getParameters()['collection'];

        self::assertInstanceOf(ResultSet::class, $result);
        self::assertSame($collection, $result->getCollection());
        self::assertSame(1000, $result->getTotalCount());

        self::assertInstanceOf(Pagerfanta::class, $event->getParameters()['pager']);
        self::assertSame(3, $event->getParameters()['pager']->getCurrentPage());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::getMaxPages
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithPagedCollection(): void
    {
        $request = Request::create('/');
        $request->query->set('page', 3);

        $this->requestStack->push($request);

        $collection = Collection::fromArray(['offset' => 3, 'limit' => 5, 'query' => new Query()]);

        $view = new BlockView(
            Block::fromArray(
                [
                    'definition' => BlockDefinition::fromArray(
                        [
                            'handlerPlugins' => [new PagedCollectionsPlugin([], [])],
                        ]
                    ),
                    'parameters' => [
                        'paged_collections:enabled' => Parameter::fromArray(['value' => true]),
                        'paged_collections:max_pages' => Parameter::fromArray(['value' => 2]),
                    ],
                    'collections' => new ArrayCollection(
                        [
                            'default' => $collection,
                        ]
                    ),
                ]
            )
        );

        $view->addParameter('collection_identifier', 'default');

        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects(self::at(0))
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(0),
                self::identicalTo(0),
                self::identicalTo(0)
            )
            ->will(self::returnValue(ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])));

        $this->resultBuilderMock
            ->expects(self::at(1))
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(8),
                self::identicalTo(5),
                self::identicalTo(0)
            )
            ->will(self::returnValue(ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])));

        $this->listener->onRenderView($event);

        $result = $event->getParameters()['collection'];

        self::assertInstanceOf(ResultSet::class, $result);
        self::assertSame($collection, $result->getCollection());
        self::assertSame(1000, $result->getTotalCount());

        self::assertInstanceOf(Pagerfanta::class, $event->getParameters()['pager']);
        self::assertSame(2, $event->getParameters()['pager']->getCurrentPage());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::getMaxPages
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithPagedCollectionAndEmptyMaxPages(): void
    {
        $request = Request::create('/');
        $request->query->set('page', 3);

        $this->requestStack->push($request);

        $collection = Collection::fromArray(['offset' => 3, 'limit' => 5, 'query' => new Query()]);

        $view = new BlockView(
            Block::fromArray(
                [
                    'definition' => BlockDefinition::fromArray(
                        [
                            'handlerPlugins' => [new PagedCollectionsPlugin([], [])],
                        ]
                    ),
                    'parameters' => [
                        'paged_collections:enabled' => Parameter::fromArray(['value' => true]),
                        'paged_collections:max_pages' => Parameter::fromArray(['value' => null]),
                    ],
                    'collections' => new ArrayCollection(
                        [
                            'default' => $collection,
                        ]
                    ),
                ]
            )
        );

        $view->addParameter('collection_identifier', 'default');

        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects(self::at(0))
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(0),
                self::identicalTo(0),
                self::identicalTo(0)
            )
            ->will(self::returnValue(ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])));

        $this->resultBuilderMock
            ->expects(self::at(1))
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(13),
                self::identicalTo(5),
                self::identicalTo(0)
            )
            ->will(self::returnValue(ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])));

        $this->listener->onRenderView($event);

        $result = $event->getParameters()['collection'];

        self::assertInstanceOf(ResultSet::class, $result);
        self::assertSame($collection, $result->getCollection());
        self::assertSame(1000, $result->getTotalCount());

        self::assertInstanceOf(Pagerfanta::class, $event->getParameters()['pager']);
        self::assertSame(3, $event->getParameters()['pager']->getCurrentPage());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::getMaxPages
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithPagedCollectionAndDisabledPaging(): void
    {
        $request = Request::create('/');
        $request->query->set('page', 3);

        $this->requestStack->push($request);

        $collection = Collection::fromArray(['offset' => 3, 'limit' => 5, 'query' => new Query()]);

        $view = new BlockView(
            Block::fromArray(
                [
                    'definition' => BlockDefinition::fromArray(
                        [
                            'handlerPlugins' => [new PagedCollectionsPlugin([], [])],
                        ]
                    ),
                    'parameters' => [
                        'paged_collections:enabled' => Parameter::fromArray(['value' => false]),
                    ],
                    'collections' => new ArrayCollection(
                        [
                            'default' => $collection,
                        ]
                    ),
                ]
            )
        );

        $view->addParameter('collection_identifier', 'default');

        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects(self::at(0))
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(0),
                self::identicalTo(0),
                self::identicalTo(0)
            )
            ->will(self::returnValue(ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])));

        $this->resultBuilderMock
            ->expects(self::at(1))
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(13),
                self::identicalTo(5),
                self::identicalTo(0)
            )
            ->will(self::returnValue(ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])));

        $this->listener->onRenderView($event);

        $result = $event->getParameters()['collection'];

        self::assertInstanceOf(ResultSet::class, $result);
        self::assertSame($collection, $result->getCollection());
        self::assertSame(1000, $result->getTotalCount());

        self::assertInstanceOf(Pagerfanta::class, $event->getParameters()['pager']);
        self::assertSame(3, $event->getParameters()['pager']->getCurrentPage());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithNoCurrentRequest(): void
    {
        $view = new BlockView(new Block());
        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $view->addParameter('collection_identifier', 'default');
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        self::assertSame([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithNoBlockView(): void
    {
        $this->requestStack->push(Request::create('/'));

        $view = new View(new Value());
        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $view->addParameter('collection_identifier', 'default');
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        self::assertSame([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithNoCollectionIdentifier(): void
    {
        $this->requestStack->push(Request::create('/'));

        $view = new BlockView(new Block());
        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        self::assertSame([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithWrongContext(): void
    {
        $this->requestStack->push(Request::create('/'));

        $view = new BlockView(new Block());
        $view->addParameter('collection_identifier', 'default');
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onRenderView($event);

        self::assertSame([], $event->getParameters());
    }
}
