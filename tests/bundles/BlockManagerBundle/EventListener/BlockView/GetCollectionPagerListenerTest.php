<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\BlockView;

use Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\CollectionReference;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
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

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->resultBuilderMock = $this->createMock(ResultBuilderInterface::class);
        $this->requestStack = new RequestStack();

        $this->listener = new GetCollectionPagerListener(
            new PagerFactory(
                $this->resultBuilderMock,
                200
            ),
            $this->requestStack,
            array(ViewInterface::CONTEXT_AJAX)
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(BlockManagerEvents::RENDER_VIEW => 'onRenderView'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::getMaxPages
     */
    public function testOnRenderView()
    {
        $request = Request::create('/');
        $request->query->set('page', 3);

        $this->requestStack->push($request);

        $collection = new Collection(array('offset' => 3, 'limit' => 5));
        $collectionReference = new CollectionReference(
            array(
                'collection' => $collection,
                'identifier' => 'default',
            )
        );

        $view = new BlockView(
            array(
                'block' => new Block(
                    array(
                        'definition' => new BlockDefinition('test'),
                        'parameters' => array(
                            'paged_collections:max_pages' => new Parameter(),
                        ),
                        'collectionReferences' => array('default' => $collectionReference),
                    )
                ),
                'collection_identifier' => 'default',
            )
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
            ->will($this->returnValue(new ResultSet(array('totalCount' => 1000, 'collection' => $collection))));

        $this->resultBuilderMock
            ->expects($this->at(1))
            ->method('build')
            ->with(
                $this->equalTo($collection),
                $this->equalTo(13),
                $this->equalTo(5),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(array('totalCount' => 1000, 'collection' => $collection))));

        $this->listener->onRenderView($event);

        $this->assertEquals(
            new ResultSet(array('totalCount' => 1000, 'collection' => $collection)),
            $event->getParameters()['collection']
        );

        $this->assertInstanceOf(Pagerfanta::class, $event->getParameters()['pager']);
        $this->assertEquals(3, $event->getParameters()['pager']->getCurrentPage());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithNoCurrentRequest()
    {
        $view = new BlockView(array('block' => new Block(), 'collection_identifier' => 'default'));
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        $this->assertEquals(array(), $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithNoBlockView()
    {
        $this->requestStack->push(Request::create('/'));

        $view = new View(array('value' => new Value(), 'collection_identifier' => 'default'));
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        $this->assertEquals(array(), $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithNoCollectionIdentifier()
    {
        $this->requestStack->push(Request::create('/'));

        $view = new BlockView(array('block' => new Block()));
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        $this->assertEquals(array(), $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithWrongContext()
    {
        $this->requestStack->push(Request::create('/'));

        $view = new BlockView(array('block' => new Block(), 'collection_identifier' => 'default'));
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onRenderView($event);

        $this->assertEquals(array(), $event->getParameters());
    }
}
