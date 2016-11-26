<?php

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\View\View\ParameterView;
use Netgen\BlockManager\View\ViewInterface;
use PHPUnit\Framework\TestCase;

class ParameterViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterValue
     */
    protected $parameterValue;

    /**
     * @var \Netgen\BlockManager\View\View\ParameterViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->parameterValue = new ParameterValue();

        $this->view = new ParameterView(
            array(
                'valueObject' => $this->parameterValue,
                'parameters' => array(
                    'parameter' => $this->parameterValue,
                ),
            )
        );

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('parameter', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ParameterView::__construct
     * @covers \Netgen\BlockManager\View\View\ParameterView::getParameterValue
     */
    public function testGetParameter()
    {
        $this->assertEquals($this->parameterValue, $this->view->getParameterValue());
    }
    /**
     * @covers \Netgen\BlockManager\View\View\ParameterView::getParameters
     */
    public function testGetParameters()
    {
        $this->assertEquals(
            array(
                'param' => 'value',
                'parameter' => $this->parameterValue,
            ),
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ParameterView::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('parameter_view', $this->view->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ParameterView::getFallbackContext
     */
    public function testGetFallbackContext()
    {
        $this->assertEquals(ViewInterface::CONTEXT_DEFAULT, $this->view->getFallbackContext());
    }
}
