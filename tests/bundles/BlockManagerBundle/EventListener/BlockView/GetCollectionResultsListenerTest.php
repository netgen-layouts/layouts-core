<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\BlockView;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\CollectionReference;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
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
        $this->assertSame(
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
        $collection1 = new Collection(['offset' => 3, 'limit' => 5, 'query' => new Query()]);
        $collectionReference1 = new CollectionReference(
            [
                'collection' => $collection1,
                'identifier' => 'collection1',
            ]
        );

        $collection2 = new Collection(['offset' => 5, 'limit' => 10, 'query' => new Query()]);
        $collectionReference2 = new CollectionReference(
            [
                'collection' => $collection2,
                'identifier' => 'collection2',
            ]
        );

        $view = new BlockView(
            new Block(
                [
                    'definition' => new BlockDefinition(),
                    'collectionReferences' => [
                        'collection1' => $collectionReference1,
                        'collection2' => $collectionReference2,
                    ],
                ]
            )
        );

        $view->setContext(ViewInterface::CONTEXT_DEFAULT);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects($this->at(0))
            ->method('build')
            ->with(
                $this->equalTo($collection1),
                $this->equalTo(3),
                $this->equalTo(5),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['collection' => $collection1, 'totalCount' => 0])));

        $this->resultBuilderMock
            ->expects($this->at(1))
            ->method('build')
            ->with(
                $this->equalTo($collection2),
                $this->equalTo(5),
                $this->equalTo(10),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['collection' => $collection2, 'totalCount' => 0])));

        $this->listener->onRenderView($event);

        $collections = $event->getParameters()['collections'];

        $this->assertArrayHasKey('collection1', $collections);
        $this->assertInstanceOf(ResultSet::class, $collections['collection1']);
        $this->assertSame($collection1, $collections['collection1']->getCollection());
        $this->assertSame(0, $collections['collection1']->getTotalCount());

        $this->assertArrayHasKey('collection2', $collections);
        $this->assertInstanceOf(ResultSet::class, $collections['collection2']);
        $this->assertSame($collection2, $collections['collection2']->getCollection());
        $this->assertSame(0, $collections['collection2']->getTotalCount());

        $this->assertInstanceOf(Pagerfanta::class, $event->getParameters()['pagers']['collection1']);
        $this->assertInstanceOf(Pagerfanta::class, $event->getParameters()['pagers']['collection2']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::getMaxPages
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithPagedCollection(): void
    {
        $collection = new Collection(['offset' => 3, 'limit' => 5, 'query' => new Query()]);
        $collectionReference = new CollectionReference(
            [
                'collection' => $collection,
                'identifier' => 'collection',
            ]
        );

        $view = new BlockView(
            new Block(
                [
                    'definition' => new BlockDefinition(
                        [
                            'handlerPlugins' => [new PagedCollectionsPlugin()],
                        ]
                    ),
                    'parameters' => [
                        'paged_collections:enabled' => new Parameter(['value' => true]),
                        'paged_collections:max_pages' => new Parameter(['value' => 2]),
                    ],
                    'collectionReferences' => [
                        'collection' => $collectionReference,
                    ],
                ]
            )
        );

        $view->setContext(ViewInterface::CONTEXT_DEFAULT);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects($this->at(0))
            ->method('build')
            ->with(
                $this->equalTo($collection),
                $this->equalTo(3),
                $this->equalTo(5),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 1000, 'collection' => $collection])));

        $this->listener->onRenderView($event);

        $collections = $event->getParameters()['collections'];

        $this->assertArrayHasKey('collection', $collections);
        $this->assertInstanceOf(ResultSet::class, $collections['collection']);
        $this->assertSame($collection, $collections['collection']->getCollection());
        $this->assertSame(1000, $collections['collection']->getTotalCount());

        $this->assertInstanceOf(Pagerfanta::class, $event->getParameters()['pagers']['collection']);
        $this->assertSame(10, $event->getParameters()['pagers']['collection']->getNbResults());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::getMaxPages
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithPagedCollectionAndEmptyMaxPages(): void
    {
        $collection = new Collection(['offset' => 3, 'limit' => 5, 'query' => new Query()]);
        $collectionReference = new CollectionReference(
            [
                'collection' => $collection,
                'identifier' => 'collection',
            ]
        );

        $view = new BlockView(
            new Block(
                [
                    'definition' => new BlockDefinition(
                        [
                            'handlerPlugins' => [new PagedCollectionsPlugin()],
                        ]
                    ),
                    'parameters' => [
                        'paged_collections:enabled' => new Parameter(['value' => true]),
                        'paged_collections:max_pages' => new Parameter(['value' => null]),
                    ],
                    'collectionReferences' => [
                        'collection' => $collectionReference,
                    ],
                ]
            )
        );

        $view->setContext(ViewInterface::CONTEXT_DEFAULT);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects($this->at(0))
            ->method('build')
            ->with(
                $this->equalTo($collection),
                $this->equalTo(3),
                $this->equalTo(5),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 1000, 'collection' => $collection])));

        $this->listener->onRenderView($event);

        $collections = $event->getParameters()['collections'];

        $this->assertArrayHasKey('collection', $collections);
        $this->assertInstanceOf(ResultSet::class, $collections['collection']);
        $this->assertSame($collection, $collections['collection']->getCollection());
        $this->assertSame(1000, $collections['collection']->getTotalCount());

        $this->assertInstanceOf(Pagerfanta::class, $event->getParameters()['pagers']['collection']);
        $this->assertSame(997, $event->getParameters()['pagers']['collection']->getNbResults());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::getMaxPages
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithPagedCollectionAndDisabledPaging(): void
    {
        $collection = new Collection(['offset' => 3, 'limit' => 5, 'query' => new Query()]);
        $collectionReference = new CollectionReference(
            [
                'collection' => $collection,
                'identifier' => 'collection',
            ]
        );

        $view = new BlockView(
            new Block(
                [
                    'definition' => new BlockDefinition(
                        [
                            'handlerPlugins' => [new PagedCollectionsPlugin()],
                        ]
                    ),
                    'parameters' => [
                        'paged_collections:enabled' => new Parameter(['value' => false]),
                    ],
                    'collectionReferences' => [
                        'collection' => $collectionReference,
                    ],
                ]
            )
        );

        $view->setContext(ViewInterface::CONTEXT_DEFAULT);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects($this->at(0))
            ->method('build')
            ->with(
                $this->equalTo($collection),
                $this->equalTo(3),
                $this->equalTo(5),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 1000, 'collection' => $collection])));

        $this->listener->onRenderView($event);

        $collections = $event->getParameters()['collections'];

        $this->assertArrayHasKey('collection', $collections);
        $this->assertInstanceOf(ResultSet::class, $collections['collection']);
        $this->assertSame($collection, $collections['collection']->getCollection());
        $this->assertSame(1000, $collections['collection']->getTotalCount());

        $this->assertInstanceOf(Pagerfanta::class, $event->getParameters()['pagers']['collection']);
        $this->assertSame(997, $event->getParameters()['pagers']['collection']->getNbResults());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::getMaxPages
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithAPIContext(): void
    {
        $collection1 = new Collection(['offset' => 3, 'limit' => 5, 'query' => new Query()]);
        $collectionReference1 = new CollectionReference(
            [
                'collection' => $collection1,
                'identifier' => 'collection1',
            ]
        );

        $view = new BlockView(
            new Block(
                [
                    'definition' => new BlockDefinition(),
                    'collectionReferences' => ['collection1' => $collectionReference1],
                ]
            )
        );

        $view->setContext(ViewInterface::CONTEXT_API);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects($this->at(0))
            ->method('build')
            ->with(
                $this->equalTo($collection1),
                $this->equalTo(3),
                $this->equalTo(5),
                $this->equalTo(ResultSet::INCLUDE_UNKNOWN_ITEMS)
            )
            ->will($this->returnValue(new ResultSet(['collection' => $collection1, 'totalCount' => 0])));

        $this->listener->onRenderView($event);

        $collections = $event->getParameters()['collections'];

        $this->assertArrayHasKey('collection1', $collections);
        $this->assertInstanceOf(ResultSet::class, $collections['collection1']);
        $this->assertSame($collection1, $collections['collection1']->getCollection());
        $this->assertSame(0, $collections['collection1']->getTotalCount());

        $this->assertInstanceOf(Pagerfanta::class, $event->getParameters()['pagers']['collection1']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithNoBlockView(): void
    {
        $view = new View(new Value());
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        $this->assertSame([], $event->getParameters());
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

        $this->assertSame([], $event->getParameters());
    }
}
