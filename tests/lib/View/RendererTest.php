<?php

namespace Netgen\BlockManager\Tests\View\Renderer;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Renderer;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\BlockManager\View\ViewInterface;
use PHPUnit\Framework\TestCase;
use Twig_Environment;

class RendererTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $twigEnvironmentMock;

    /**
     * @var \Netgen\BlockManager\View\RendererInterface
     */
    protected $viewRenderer;

    public function setUp()
    {
        $this->viewBuilderMock = $this
            ->createMock(ViewBuilderInterface::class);

        $this->twigEnvironmentMock = $this
            ->createMock(Twig_Environment::class);

        $this->viewRenderer = new Renderer($this->viewBuilderMock, $this->twigEnvironmentMock);
    }

    /**
     * @covers \Netgen\BlockManager\View\Renderer::__construct
     * @covers \Netgen\BlockManager\View\Renderer::renderValueObject
     */
    public function testRenderValueObject()
    {
        $view = new View(array('value' => new Value()));
        $view->setContext(ViewInterface::CONTEXT_API);
        $view->setTemplate('some_template.html.twig');
        $view->addParameter('some_param', 'some_value');

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->with(new Value(), array('some_param' => 'some_value'), ViewInterface::CONTEXT_API)
            ->will($this->returnValue($view));

        $this->twigEnvironmentMock
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('some_template.html.twig'),
                $this->equalTo(
                    array(
                        'value' => new Value(),
                        'some_param' => 'some_value',
                    )
                )
            )
            ->will($this->returnValue('rendered template'));

        $renderedTemplate = $this->viewRenderer->renderValueObject(
            new Value(),
            array('some_param' => 'some_value'),
            ViewInterface::CONTEXT_API
        );

        $this->assertEquals('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\View\Renderer::__construct
     * @covers \Netgen\BlockManager\View\Renderer::renderView
     */
    public function testRenderView()
    {
        $view = new View(array('value' => new Value()));
        $view->setTemplate('some_template.html.twig');
        $view->addParameter('some_param', 'some_value');

        $this->twigEnvironmentMock
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('some_template.html.twig'),
                $this->equalTo(
                    array(
                        'some_param' => 'some_value',
                        'value' => new Value(),
                    )
                )
            )
            ->will($this->returnValue('rendered template'));

        $renderedTemplate = $this->viewRenderer->renderView($view);

        $this->assertEquals('rendered template', $renderedTemplate);
    }
}
