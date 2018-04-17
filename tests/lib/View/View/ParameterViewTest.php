<?php

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterType;
use Netgen\BlockManager\View\View\ParameterView;
use PHPUnit\Framework\TestCase;

final class ParameterViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Parameter
     */
    private $parameter;

    /**
     * @var \Netgen\BlockManager\View\View\ParameterViewInterface
     */
    private $view;

    public function setUp()
    {
        $this->parameter = new Parameter(
            [
                'name' => 'paramName',
                'parameterDefinition' => new ParameterDefinition(
                    [
                        'type' => new ParameterType(),
                    ]
                ),
            ]
        );

        $this->view = new ParameterView(
            [
                'parameter' => $this->parameter,
            ]
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
        $this->assertEquals($this->parameter, $this->view->getParameterValue());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ParameterView::getParameters
     */
    public function testGetParameters()
    {
        $this->assertEquals(
            [
                'param' => 'value',
                'parameter' => $this->parameter,
            ],
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
     * @covers \Netgen\BlockManager\View\View\ParameterView::jsonSerialize
     */
    public function testJsonSerialize()
    {
        $this->assertEquals(
            [
                'name' => 'paramName',
                'type' => 'type',
            ],
            $this->view->jsonSerialize()
        );
    }
}
