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
        $this->assertEquals(
            [BlockManagerEvents::RENDER_VIEW => 'onRenderView'],
            $this->listener->getSubscribedEvents()
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

        $collection = new Collection(['offset' => 3, 'limit' => 5, 'query' => new Query()]);
        $collectionReference = new CollectionReference(
            [
                'collection' => $collection,
                'identifier' => 'default',
            ]
        );

        $view = new BlockView(
            [
                'block' => new Block(
                    [
                        'definition' => new BlockDefinition(),
                        'collectionReferences' => ['default' => $collectionReference],
                    ]
                ),
                'collection_identifier' => 'default',
            ]
        );

        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects($this->at(0))
            ->method('build')
            ->with(
                $this->equalTo($collection),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 1000, 'collection' => $collection])));

        $this->resultBuilderMock
            ->expects($this->at(1))
            ->method('build')
            ->with(
                $this->equalTo($collection),
                $this->equalTo(13),
                $this->equalTo(5),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 1000, 'collection' => $collection])));

        $this->listener->onRenderView($event);

        $this->assertEquals(
            new ResultSet(['totalCount' => 1000, 'collection' => $collection]),
            $event->getParameters()['collection']
        );

        $this->assertInstanceOf(Pagerfanta::class, $event->getParameters()['pager']);
        $this->assertEquals(3, $event->getParameters()['pager']->getCurrentPage());
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

        $collection = new Collection(['offset' => 3, 'limit' => 5, 'query' => new Query()]);
        $collectionReference = new CollectionReference(
            [
                'collection' => $collection,
                'identifier' => 'default',
            ]
        );

        $view = new BlockView(
            [
                'block' => new Block(
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
                        'collectionReferences' => ['default' => $collectionReference],
                    ]
                ),
                'collection_identifier' => 'default',
            ]
        );

        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects($this->at(0))
            ->method('build')
            ->with(
                $this->equalTo($collection),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 1000, 'collection' => $collection])));

        $this->resultBuilderMock
            ->expects($this->at(1))
            ->method('build')
            ->with(
                $this->equalTo($collection),
                $this->equalTo(8),
                $this->equalTo(5),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 1000, 'collection' => $collection])));

        $this->listener->onRenderView($event);

        $this->assertEquals(
            new ResultSet(['totalCount' => 1000, 'collection' => $collection]),
            $event->getParameters()['collection']
        );

        $this->assertInstanceOf(Pagerfanta::class, $event->getParameters()['pager']);
        $this->assertEquals(2, $event->getParameters()['pager']->getCurrentPage());
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

        $collection = new Collection(['offset' => 3, 'limit' => 5, 'query' => new Query()]);
        $collectionReference = new CollectionReference(
            [
                'collection' => $collection,
                'identifier' => 'default',
            ]
        );

        $view = new BlockView(
            [
                'block' => new Block(
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
                        'collectionReferences' => ['default' => $collectionReference],
                    ]
                ),
                'collection_identifier' => 'default',
            ]
        );

        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects($this->at(0))
            ->method('build')
            ->with(
                $this->equalTo($collection),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 1000, 'collection' => $collection])));

        $this->resultBuilderMock
            ->expects($this->at(1))
            ->method('build')
            ->with(
                $this->equalTo($collection),
                $this->equalTo(13),
                $this->equalTo(5),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 1000, 'collection' => $collection])));

        $this->listener->onRenderView($event);

        $this->assertEquals(
            new ResultSet(['totalCount' => 1000, 'collection' => $collection]),
            $event->getParameters()['collection']
        );

        $this->assertInstanceOf(Pagerfanta::class, $event->getParameters()['pager']);
        $this->assertEquals(3, $event->getParameters()['pager']->getCurrentPage());
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

        $collection = new Collection(['offset' => 3, 'limit' => 5, 'query' => new Query()]);
        $collectionReference = new CollectionReference(
            [
                'collection' => $collection,
                'identifier' => 'default',
            ]
        );

        $view = new BlockView(
            [
                'block' => new Block(
                    [
                        'definition' => new BlockDefinition(
                            [
                                'handlerPlugins' => [new PagedCollectionsPlugin()],
                            ]
                        ),
                        'parameters' => [
                            'paged_collections:enabled' => new Parameter(['value' => false]),
                        ],
                        'collectionReferences' => ['default' => $collectionReference],
                    ]
                ),
                'collection_identifier' => 'default',
            ]
        );

        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects($this->at(0))
            ->method('build')
            ->with(
                $this->equalTo($collection),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 1000, 'collection' => $collection])));

        $this->resultBuilderMock
            ->expects($this->at(1))
            ->method('build')
            ->with(
                $this->equalTo($collection),
                $this->equalTo(13),
                $this->equalTo(5),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(['totalCount' => 1000, 'collection' => $collection])));

        $this->listener->onRenderView($event);

        $this->assertEquals(
            new ResultSet(['totalCount' => 1000, 'collection' => $collection]),
            $event->getParameters()['collection']
        );

        $this->assertInstanceOf(Pagerfanta::class, $event->getParameters()['pager']);
        $this->assertEquals(3, $event->getParameters()['pager']->getCurrentPage());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithNoCurrentRequest(): void
    {
        $view = new BlockView(['block' => new Block(), 'collection_identifier' => 'default']);
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        $this->assertEquals([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithNoBlockView(): void
    {
        $this->requestStack->push(Request::create('/'));

        $view = new View(['value' => new Value(), 'collection_identifier' => 'default']);
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        $this->assertEquals([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithNoCollectionIdentifier(): void
    {
        $this->requestStack->push(Request::create('/'));

        $view = new BlockView(['block' => new Block()]);
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        $this->assertEquals([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithWrongContext(): void
    {
        $this->requestStack->push(Request::create('/'));

        $view = new BlockView(['block' => new Block(), 'collection_identifier' => 'default']);
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onRenderView($event);

        $this->assertEquals([], $event->getParameters());
    }
}
