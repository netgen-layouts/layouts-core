<?php

namespace Netgen\BlockManager\Tests\View;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Provider\ViewProviderInterface;
use Netgen\BlockManager\View\ViewBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Netgen\BlockManager\View\TemplateResolverInterface;
use DateTime;

class ViewBuilderTest extends \PHPUnit_Framework_TestCase
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
        $this->viewProviderMock = $this->getMock(ViewProviderInterface::class);
        $this->templateResolverMock = $this->getMock(TemplateResolverInterface::class);
        $this->eventDispatcherMock = $this->getMock(EventDispatcherInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @expectedException \RuntimeException
     */
    public function testConstructorThrowsRuntimeExceptionWithNoViewProviderInterface()
    {
        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock,
            array($this->getMock(DateTime::class))
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::__construct
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     */
    public function testBuildView()
    {
        $value = new Value();
        $view = new View($value);

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
            ->with($this->equalTo($view))
            ->will($this->returnValue('some_template.html.twig'));

        $this->eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch');

        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock,
            array($this->viewProviderMock)
        );

        $viewParameters = array('some_param' => 'some_value');
        $builtView = $viewBuilder->buildView($value, 'context', $viewParameters);

        self::assertInstanceOf(View::class, $builtView);
        self::assertEquals('some_template.html.twig', $builtView->getTemplate());
        self::assertEquals('context', $builtView->getContext());
        self::assertEquals(
            array('view_context' => $builtView->getContext()) + $viewParameters,
            $builtView->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @expectedException \RuntimeException
     */
    public function testBuildViewWithNoViewProviders()
    {
        $value = new Value();

        $viewBuilder = new ViewBuilder(
            $this->templateResolverMock,
            $this->eventDispatcherMock
        );

        $viewBuilder->buildView($value, 'context');
    }

    /**
     * @covers \Netgen\BlockManager\View\ViewBuilder::buildView
     * @expectedException \RuntimeException
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

        $viewBuilder->buildView($value, 'context');
    }
}
