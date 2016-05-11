<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\Bundle\BlockManagerBundle\EventListener\APIBlockViewListener;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\View\LayoutView;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
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
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\APIBlockViewListener
     */
    protected $listener;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->blockServiceMock = $this->getMock(
            BlockService::class
        );

        $this->configurationMock = $this->getMock(
            ConfigurationInterface::class
        );

        $this->formFactoryMock = $this->getMock(FormFactoryInterface::class);
        $this->formMock = $this->getMock(FormInterface::class);

        $this->formMock
            ->expects($this->any())
            ->method('createView')
            ->will($this->returnValue(new FormView()));

        $this->listener = new APIBlockViewListener(
            $this->blockServiceMock,
            $this->configurationMock,
            $this->formFactoryMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\EventListener\APIBlockViewListener::__construct
     * @covers \Netgen\BlockManager\EventListener\APIBlockViewListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        self::assertEquals(
            array(ViewEvents::BUILD_VIEW => 'onBuildView'),
            $this->listener->getSubscribedEvents()
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

        $blockConfig = array('forms' => array('inline_edit' => 'inline_edit_form'));

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
                $this->equalTo('inline_edit_form'),
                $this->equalTo($blockUpdateStruct),
                $this->equalTo(array('block' => $block))
            )
            ->will($this->returnValue($this->formMock));

        $event = new CollectViewParametersEvent($blockView);

        $this->listener->onBuildView($event);

        self::assertTrue($event->getParameterBag()->has('form'));
        self::assertInstanceOf(FormView::class, $event->getParameterBag()->get('form'));
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

        $this->listener->onBuildView($event);

        self::assertFalse($event->getParameterBag()->has('form'));
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

        $this->listener->onBuildView($event);

        self::assertFalse($event->getParameterBag()->has('form'));
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

        $this->listener->onBuildView($event);

        self::assertFalse($event->getParameterBag()->has('form'));
    }
}
