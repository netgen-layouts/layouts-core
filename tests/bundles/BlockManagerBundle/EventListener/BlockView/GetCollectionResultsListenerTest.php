<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\BlockView;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;

final class GetCollectionResultsListenerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $resultBuilderMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->resultBuilderMock = $this->createMock(ResultBuilderInterface::class);

        $this->listener = new GetCollectionResultsListener(
            new PagerFactory(
                $this->resultBuilderMock,
                200
            ),
            [ViewInterface::CONTEXT_DEFAULT, ViewInterface::CONTEXT_API]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [sprintf('%s.%s', BlockManagerEvents::RENDER_VIEW, 'block') => 'onRenderView'],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::getMaxPages
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderView(): void
    {
        $collection1 = Collection::fromArray(['offset' => 3, 'limit' => 5, 'query' => new Query()]);
        $collection2 = Collection::fromArray(['offset' => 5, 'limit' => 10, 'query' => new Query()]);

        $view = new BlockView(
            Block::fromArray(
                [
                    'definition' => new BlockDefinition(),
                    'collections' => new ArrayCollection(
                        [
                            'collection1' => $collection1,
                            'collection2' => $collection2,
                        ]
                    ),
                ]
            )
        );

        $view->setContext(ViewInterface::CONTEXT_DEFAULT);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects(self::at(0))
            ->method('build')
            ->with(
                self::identicalTo($collection1),
                self::identicalTo(3),
                self::identicalTo(5),
                self::identicalTo(0)
            )
            ->will(self::returnValue(ResultSet::fromArray(['collection' => $collection1, 'totalCount' => 0])));

        $this->resultBuilderMock
            ->expects(self::at(1))
            ->method('build')
            ->with(
                self::identicalTo($collection2),
                self::identicalTo(5),
                self::identicalTo(10),
                self::identicalTo(0)
            )
            ->will(self::returnValue(ResultSet::fromArray(['collection' => $collection2, 'totalCount' => 0])));

        $this->listener->onRenderView($event);

        $collections = $event->getParameters()['collections'];

        self::assertArrayHasKey('collection1', $collections);
        self::assertInstanceOf(ResultSet::class, $collections['collection1']);
        self::assertSame($collection1, $collections['collection1']->getCollection());
        self::assertSame(0, $collections['collection1']->getTotalCount());

        self::assertArrayHasKey('collection2', $collections);
        self::assertInstanceOf(ResultSet::class, $collections['collection2']);
        self::assertSame($collection2, $collections['collection2']->getCollection());
        self::assertSame(0, $collections['collection2']->getTotalCount());

        self::assertInstanceOf(Pagerfanta::class, $event->getParameters()['pagers']['collection1']);
        self::assertInstanceOf(Pagerfanta::class, $event->getParameters()['pagers']['collection2']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::getMaxPages
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithPagedCollection(): void
    {
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
                            'collection' => $collection,
                        ]
                    ),
                ]
            )
        );

        $view->setContext(ViewInterface::CONTEXT_DEFAULT);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects(self::at(0))
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(3),
                self::identicalTo(5),
                self::identicalTo(0)
            )
            ->will(self::returnValue(ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])));

        $this->listener->onRenderView($event);

        $collections = $event->getParameters()['collections'];

        self::assertArrayHasKey('collection', $collections);
        self::assertInstanceOf(ResultSet::class, $collections['collection']);
        self::assertSame($collection, $collections['collection']->getCollection());
        self::assertSame(1000, $collections['collection']->getTotalCount());

        self::assertInstanceOf(Pagerfanta::class, $event->getParameters()['pagers']['collection']);
        self::assertSame(10, $event->getParameters()['pagers']['collection']->getNbResults());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::getMaxPages
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithPagedCollectionAndEmptyMaxPages(): void
    {
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
                            'collection' => $collection,
                        ]
                    ),
                ]
            )
        );

        $view->setContext(ViewInterface::CONTEXT_DEFAULT);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects(self::at(0))
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(3),
                self::identicalTo(5),
                self::identicalTo(0)
            )
            ->will(self::returnValue(ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])));

        $this->listener->onRenderView($event);

        $collections = $event->getParameters()['collections'];

        self::assertArrayHasKey('collection', $collections);
        self::assertInstanceOf(ResultSet::class, $collections['collection']);
        self::assertSame($collection, $collections['collection']->getCollection());
        self::assertSame(1000, $collections['collection']->getTotalCount());

        self::assertInstanceOf(Pagerfanta::class, $event->getParameters()['pagers']['collection']);
        self::assertSame(997, $event->getParameters()['pagers']['collection']->getNbResults());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::getMaxPages
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithPagedCollectionAndDisabledPaging(): void
    {
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
                            'collection' => $collection,
                        ]
                    ),
                ]
            )
        );

        $view->setContext(ViewInterface::CONTEXT_DEFAULT);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects(self::at(0))
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(3),
                self::identicalTo(5),
                self::identicalTo(0)
            )
            ->will(self::returnValue(ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])));

        $this->listener->onRenderView($event);

        $collections = $event->getParameters()['collections'];

        self::assertArrayHasKey('collection', $collections);
        self::assertInstanceOf(ResultSet::class, $collections['collection']);
        self::assertSame($collection, $collections['collection']->getCollection());
        self::assertSame(1000, $collections['collection']->getTotalCount());

        self::assertInstanceOf(Pagerfanta::class, $event->getParameters()['pagers']['collection']);
        self::assertSame(997, $event->getParameters()['pagers']['collection']->getNbResults());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::getMaxPages
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithAPIContext(): void
    {
        $collection1 = Collection::fromArray(['offset' => 3, 'limit' => 5, 'query' => new Query()]);

        $view = new BlockView(
            Block::fromArray(
                [
                    'definition' => new BlockDefinition(),
                    'collections' => new ArrayCollection(
                        [
                            'collection1' => $collection1,
                        ]
                    ),
                ]
            )
        );

        $view->setContext(ViewInterface::CONTEXT_API);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects(self::at(0))
            ->method('build')
            ->with(
                self::identicalTo($collection1),
                self::identicalTo(3),
                self::identicalTo(5),
                self::identicalTo(ResultSet::INCLUDE_UNKNOWN_ITEMS)
            )
            ->will(self::returnValue(ResultSet::fromArray(['collection' => $collection1, 'totalCount' => 0])));

        $this->listener->onRenderView($event);

        $collections = $event->getParameters()['collections'];

        self::assertArrayHasKey('collection1', $collections);
        self::assertInstanceOf(ResultSet::class, $collections['collection1']);
        self::assertSame($collection1, $collections['collection1']->getCollection());
        self::assertSame(0, $collections['collection1']->getTotalCount());

        self::assertInstanceOf(Pagerfanta::class, $event->getParameters()['pagers']['collection1']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithNoBlockView(): void
    {
        $view = new View(new Value());
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        self::assertSame([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithWrongContext(): void
    {
        $view = new BlockView(new Block());
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onRenderView($event);

        self::assertSame([], $event->getParameters());
    }
}
