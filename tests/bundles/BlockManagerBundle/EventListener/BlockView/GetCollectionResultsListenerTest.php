<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\BlockView;

use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\CollectionReference;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener;
use PHPUnit\Framework\TestCase;

class GetCollectionResultsListenerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resultBuilderMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener
     */
    private $listener;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->resultBuilderMock = $this->createMock(ResultBuilderInterface::class);

        $this->listener = new GetCollectionResultsListener(
            $this->resultBuilderMock,
            25,
            array(ViewInterface::CONTEXT_DEFAULT, ViewInterface::CONTEXT_API)
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(BlockManagerEvents::RENDER_VIEW => 'onRenderView'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderView()
    {
        $collectionReference1 = new CollectionReference(
            array(
                'collection' => new Collection(),
                'identifier' => 'collection1',
                'offset' => 3,
                'limit' => 5,
            )
        );

        $collectionReference2 = new CollectionReference(
            array(
                'collection' => new Collection(),
                'identifier' => 'collection2',
                'offset' => 5,
                'limit' => 10,
            )
        );

        $view = new BlockView(array('block' => new Block(array('collectionReferences' => array($collectionReference1, $collectionReference2)))));
        $view->setContext(ViewInterface::CONTEXT_DEFAULT);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects($this->at(0))
            ->method('build')
            ->with(
                $this->equalTo(new Collection()),
                $this->equalTo(3),
                $this->equalTo(5),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(array('collection' => new Collection()))));

        $this->resultBuilderMock
            ->expects($this->at(1))
            ->method('build')
            ->with(
                $this->equalTo(new Collection()),
                $this->equalTo(5),
                $this->equalTo(10),
                $this->equalTo(0)
            )
            ->will($this->returnValue(new ResultSet(array('collection' => new Collection()))));

        $this->listener->onRenderView($event);

        $this->assertEquals(
            array(
                'collections' => array(
                    'collection1' => new ResultSet(array('collection' => new Collection())),
                    'collection2' => new ResultSet(array('collection' => new Collection())),
                ),
            ),
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithAPIContext()
    {
        $collectionReference1 = new CollectionReference(
            array(
                'collection' => new Collection(),
                'identifier' => 'collection1',
                'offset' => 3,
                'limit' => 5,
            )
        );

        $view = new BlockView(array('block' => new Block(array('collectionReferences' => array($collectionReference1)))));
        $view->setContext(ViewInterface::CONTEXT_API);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects($this->at(0))
            ->method('build')
            ->with(
                $this->equalTo(new Collection()),
                $this->equalTo(3),
                $this->equalTo(5),
                $this->equalTo(ResultSet::INCLUDE_UNKNOWN_ITEMS)
            )
            ->will($this->returnValue(new ResultSet(array('collection' => new Collection()))));

        $this->listener->onRenderView($event);

        $this->assertEquals(
            array(
                'collections' => array(
                    'collection1' => new ResultSet(array('collection' => new Collection())),
                ),
            ),
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithEmptyLimit()
    {
        $collectionReference = new CollectionReference(
            array(
                'collection' => new Collection(),
                'identifier' => 'collection',
                'offset' => 3,
                'limit' => null,
            )
        );

        $view = new BlockView(array('block' => new Block(array('collectionReferences' => array($collectionReference)))));
        $view->setContext(ViewInterface::CONTEXT_DEFAULT);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects($this->at(0))
            ->method('build')
            ->with(
                $this->equalTo(new Collection()),
                $this->equalTo(3),
                $this->equalTo(25)
            )
            ->will($this->returnValue(new ResultSet(array('collection' => new Collection()))));

        $this->listener->onRenderView($event);

        $this->assertEquals(
            array(
                'collections' => array(
                    'collection' => new ResultSet(array('collection' => new Collection())),
                ),
            ),
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithTooLargeLimit()
    {
        $collectionReference = new CollectionReference(
            array(
                'collection' => new Collection(),
                'identifier' => 'collection',
                'offset' => 3,
                'limit' => 9999,
            )
        );

        $view = new BlockView(array('block' => new Block(array('collectionReferences' => array($collectionReference)))));
        $view->setContext(ViewInterface::CONTEXT_DEFAULT);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->expects($this->at(0))
            ->method('build')
            ->with(
                $this->equalTo(new Collection()),
                $this->equalTo(3),
                $this->equalTo(25)
            )
            ->will($this->returnValue(new ResultSet(array('collection' => new Collection()))));

        $this->listener->onRenderView($event);

        $this->assertEquals(
            array(
                'collections' => array(
                    'collection' => new ResultSet(array('collection' => new Collection())),
                ),
            ),
            $event->getParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithNoBlockView()
    {
        $view = new View(array('value' => new Value()));
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        $this->assertEquals(array(), $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onRenderView
     */
    public function testOnRenderViewWithWrongContext()
    {
        $view = new BlockView(array('block' => new Block()));
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onRenderView($event);

        $this->assertEquals(array(), $event->getParameters());
    }
}
