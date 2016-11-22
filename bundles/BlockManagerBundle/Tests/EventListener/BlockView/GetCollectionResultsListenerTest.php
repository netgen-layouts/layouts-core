<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\BlockView;

use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Page\CollectionReference;
use Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Collection\Result\ResultLoaderInterface;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\View\ViewInterface;
use PHPUnit\Framework\TestCase;

class GetCollectionResultsListenerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultLoaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener
     */
    protected $listener;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->resultLoaderMock = $this->createMock(ResultLoaderInterface::class);
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->listener = new GetCollectionResultsListener(
            $this->resultLoaderMock,
            $this->blockServiceMock,
            25,
            array(ViewInterface::CONTEXT_DEFAULT)
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(BlockManagerEvents::BUILD_VIEW => 'onBuildView'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onBuildView
     */
    public function testOnBuildView()
    {
        $blockDefinition = new BlockDefinition('def');

        $collectionReference1 = new CollectionReference(
            array(
                'block' => new Block(),
                'collection' => new Collection(),
                'identifier' => 'collection1',
                'offset' => 3,
                'limit' => 5,
            )
        );

        $collectionReference2 = new CollectionReference(
            array(
                'block' => new Block(),
                'collection' => new Collection(),
                'identifier' => 'collection2',
                'offset' => 5,
                'limit' => 10,
            )
        );

        $view = new BlockView(new Block(), $blockDefinition);
        $view->setContext(ViewInterface::CONTEXT_DEFAULT);
        $event = new CollectViewParametersEvent($view);

        $this->blockServiceMock
            ->expects($this->once())
            ->method('loadCollectionReferences')
            ->with($this->equalTo(new Block()))
            ->will($this->returnValue(array($collectionReference1, $collectionReference2)));

        $this->resultLoaderMock
            ->expects($this->at(0))
            ->method('load')
            ->with($this->equalTo(new Collection()))
            ->will($this->returnValue(new ResultSet(array('collection' => new Collection()))));

        $this->resultLoaderMock
            ->expects($this->at(1))
            ->method('load')
            ->with($this->equalTo(new Collection()))
            ->will($this->returnValue(new ResultSet(array('collection' => new Collection()))));

        $this->listener->onBuildView($event);

        $this->assertEquals(
            array(
                'collections' => array(
                    'collection1' => new ResultSet(array('collection' => new Collection())),
                    'collection2' => new ResultSet(array('collection' => new Collection())),
                ),
            ),
            $event->getViewParameters()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onBuildView
     */
    public function testOnBuildViewWithNoBlockView()
    {
        $view = new View(new Value());
        $event = new CollectViewParametersEvent($view);
        $this->listener->onBuildView($event);

        $this->assertEquals(array(), $event->getViewParameters());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onBuildView
     */
    public function testOnBuildViewWithWrongContext()
    {
        $blockDefinition = new BlockDefinition('def');

        $view = new BlockView(new Block(), $blockDefinition);
        $view->setContext(ViewInterface::CONTEXT_API);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        $this->assertEquals(array(), $event->getViewParameters());
    }
}
