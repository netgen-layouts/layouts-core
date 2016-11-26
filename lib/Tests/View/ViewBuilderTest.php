<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Provider\ViewProviderInterface;
use Netgen\BlockManager\View\ViewBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Netgen\BlockManager\View\TemplateResolverInterface;
use DateTime;
use PHPUnit\Framework\TestCase;

class ViewBuilderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventDispatcherMock;

    public function setUp()
    {
        $this->viewProviderMock = $this->createMock(ViewProviderInterface::class);
        $this->templateResolverMock = $this->createMock(TemplateResolverInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testConstructorThrowsRuntimeExceptionWithNoViewProviderInterface()
    {
        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock,
            array($this->createMock(DateTime::class))
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @covers \Netgen\BlockManager\View\ViewBuilder::getViewProvider
     */
    public function testBuildView()
    {
        $value = new Value();
        $view = new View(array('valueObject' => $value));

        $this->viewProviderMock
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(true));

        $this->viewProviderMock
            ->expects($this->once())
            ->method('provideView')
            ->with($this->equalTo($value))
            ->will($this->returnValue($view));

        $this->templateResolverMock
            ->expects($this->once())
            ->method('resolveTemplate')
            ->with($this->equalTo($view));

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch');

        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock,
            array($this->viewProviderMock)
        );

        $viewParameters = array('some_param' => 'some_value');
        $builtView = $viewBuilder->buildView($value, $viewParameters, 'context');

        $this->assertInstanceOf(View::class, $builtView);
        $this->assertEquals('context', $builtView->getContext());
        $this->assertEquals(
            array('view_context' => $builtView->getContext()) + $viewParameters,
            $builtView->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @covers \Netgen\BlockManager\View\ViewBuilder::getViewProvider
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testBuildViewWithNoViewProviders()
    {
        $value = new Value();

        $this->templateResolverMock
            ->expects($this->never())
            ->method('resolveTemplate');

        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock
        );

        $viewBuilder->buildView($value);
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @covers \Netgen\BlockManager\View\ViewBuilder::getViewProvider
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testBuildViewWithNoViewProvidersThatSupportValue()
    {
        $value = new Value();

        $this->viewProviderMock
            ->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($value))
            ->will($this->returnValue(false));

        $this->viewProviderMock
            ->expects($this->never())
            ->method('provideView');

        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock,
            array($this->viewProviderMock)
        );

        $viewBuilder->buildView($value);
    }
}
