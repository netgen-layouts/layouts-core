<?php

namespace Netgen\BlockManager\Tests\EventListener;

use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\EventListener\APIBlockViewListener;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\View\LayoutView;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\Form\FormView;

class APIBlockViewListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formMock;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->blockServiceMock = $this->getMock(
            'Netgen\BlockManager\API\Service\BlockService'
        );

        $this->configurationMock = $this->getMock(
            'Netgen\BlockManager\Configuration\ConfigurationInterface'
        );

        $this->formFactoryMock = $this->getMock(
            'Symfony\Component\Form\FormFactoryInterface'
        );

        $this->formMock = $this->getMock(
            'Symfony\Component\Form\FormInterface'
        );

        $this->formMock
            ->expects($this->any())
            ->method('createView')
            ->will($this->returnValue(new FormView()));
    }

    /**
     * @covers \Netgen\BlockManager\EventListener\APIBlockViewListener::__construct
     * @covers \Netgen\BlockManager\EventListener\APIBlockViewListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $listener = $this->getEventListener();
        self::assertEquals(
            array(ViewEvents::BUILD_VIEW => 'onBuildView'),
            $listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\BlockManager\EventListener\APIBlockViewListener::onBuildView
     */
    public function testOnBuildView()
    {
        $block = new Block(array('definitionIdentifier' => 'block'));
        $blockView = new BlockView();
        $blockView->setContext(ViewInterface::CONTEXT_API);
        $blockView->setBlock($block);

        $blockConfig = array('forms' => array('inline' => 'inline_form'));

        $this->configurationMock
            ->expects($this->once())
            ->method('getBlockConfig')
            ->with($this->equalTo('block'))
            ->will($this->returnValue($blockConfig));

        $blockUpdateStruct = new BlockUpdateStruct();

        $this->blockServiceMock
            ->expects($this->any())
            ->method('newBlockUpdateStruct')
            ->will($this->returnValue($blockUpdateStruct));

        $this->formFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('inline_form'),
                $this->equalTo($blockUpdateStruct),
                $this->equalTo(array('block' => $block))
            )
            ->will($this->returnValue($this->formMock));

        $event = new CollectViewParametersEvent($blockView);

        $listener = $this->getEventListener();
        $listener->onBuildView($event);

        self::assertEquals(true, $event->getParameterBag()->has('form'));
        self::assertInstanceOf('Symfony\Component\Form\FormView', $event->getParameterBag()->get('form'));
    }

    /**
     * @covers \Netgen\BlockManager\EventListener\APIBlockViewListener::onBuildView
     */
    public function testOnBuildViewWithNoBlockView()
    {
        $layoutView = new LayoutView();

        $this->configurationMock
            ->expects($this->never())
            ->method('getBlockConfig');

        $this->blockServiceMock
            ->expects($this->never())
            ->method('newBlockUpdateStruct');

        $this->formFactoryMock
            ->expects($this->never())
            ->method('create');

        $event = new CollectViewParametersEvent($layoutView);

        $listener = $this->getEventListener();
        $listener->onBuildView($event);

        self::assertEquals(false, $event->getParameterBag()->has('form'));
    }

    /**
     * @covers \Netgen\BlockManager\EventListener\APIBlockViewListener::onBuildView
     */
    public function testOnBuildViewWithNoApiContext()
    {
        $blockView = new BlockView();

        $this->configurationMock
            ->expects($this->never())
            ->method('getBlockConfig');

        $this->blockServiceMock
            ->expects($this->never())
            ->method('newBlockUpdateStruct');

        $this->formFactoryMock
            ->expects($this->never())
            ->method('create');

        $event = new CollectViewParametersEvent($blockView);

        $listener = $this->getEventListener();
        $listener->onBuildView($event);

        self::assertEquals(false, $event->getParameterBag()->has('form'));
    }

    /**
     * @covers \Netgen\BlockManager\EventListener\APIBlockViewListener::onBuildView
     */
    public function testOnBuildViewWithNoInlineForm()
    {
        $block = new Block(array('definitionIdentifier' => 'block'));
        $blockView = new BlockView();
        $blockView->setContext(ViewInterface::CONTEXT_API);
        $blockView->setBlock($block);

        $this->configurationMock
            ->expects($this->once())
            ->method('getBlockConfig')
            ->with($this->equalTo('block'))
            ->will($this->returnValue(array()));

        $this->blockServiceMock
            ->expects($this->never())
            ->method('newBlockUpdateStruct');

        $this->formFactoryMock
            ->expects($this->never())
            ->method('create');

        $event = new CollectViewParametersEvent($blockView);

        $listener = $this->getEventListener();
        $listener->onBuildView($event);

        self::assertEquals(false, $event->getParameterBag()->has('form'));
    }

    /**
     * Returns the listener under test.
     *
     * @return \Netgen\BlockManager\EventListener\APIBlockViewListener
     */
    protected function getEventListener()
    {
        return new APIBlockViewListener(
            $this->blockServiceMock,
            $this->configurationMock,
            $this->formFactoryMock
        );
    }
}
