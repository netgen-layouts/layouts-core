<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\BlockView;

use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Page\CollectionReference;
use Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Collection\Result;
use Netgen\BlockManager\Collection\ResultGeneratorInterface;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\View\ViewInterface;
use PHPUnit\Framework\TestCase;

class GetCollectionResultsListenerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultGeneratorMock;

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
        $this->resultGeneratorMock = $this->createMock(ResultGeneratorInterface::class);
        $this->blockServiceMock = $this->createMock(BlockService::class);

        $this->listener = new GetCollectionResultsListener(
            $this->resultGeneratorMock,
            $this->blockServiceMock,
            array(ViewInterface::CONTEXT_DEFAULT)
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(ViewEvents::BUILD_VIEW => 'onBuildView'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\BlockView\GetCollectionResultsListener::onBuildView
     */
    public function testOnBuildView()
    {
        $blockDefinition = new BlockDefinition(
            'def',
            $this->createMock(BlockDefinitionHandlerInterface::class),
            $this->createMock(Configuration::class)
        );

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

        $this->resultGeneratorMock
            ->expects($this->at(0))
            ->method('generateResult')
            ->with($this->equalTo(new Collection()))
            ->will($this->returnValue(new Result(array('collection' => new Collection()))));

        $this->resultGeneratorMock
            ->expects($this->at(1))
            ->method('generateResult')
            ->with($this->equalTo(new Collection()))
            ->will($this->returnValue(new Result(array('collection' => new Collection()))));

        $this->listener->onBuildView($event);

        $this->assertEquals(
            array(
                'collections' => array(
                    'collection1' => new Result(array('collection' => new Collection())),
                    'collection2' => new Result(array('collection' => new Collection())),
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
        $blockDefinition = new BlockDefinition(
            'def',
            $this->createMock(BlockDefinitionHandlerInterface::class),
            $this->createMock(Configuration::class)
        );

        $view = new BlockView(new Block(), $blockDefinition);
        $view->setContext(ViewInterface::CONTEXT_API);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onBuildView($event);

        $this->assertEquals(array(), $event->getViewParameters());
    }
}
