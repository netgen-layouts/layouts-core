<?php

declare(strict_types=1);

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

    public function setUp(): void
    {
        $this->parameter = Parameter::fromArray(
            [
                'name' => 'paramName',
                'parameterDefinition' => ParameterDefinition::fromArray(
                    [
                        'type' => new ParameterType(),
                    ]
                ),
            ]
        );

        $this->view = new ParameterView($this->parameter);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('parameter', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ParameterView::__construct
     * @covers \Netgen\BlockManager\View\View\ParameterView::getParameterValue
     */
    public function testGetParameter(): void
    {
        self::assertSame($this->parameter, $this->view->getParameterValue());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ParameterView::getParameters
     */
    public function testGetParameters(): void
    {
        self::assertSame(
            [
                'parameter' => $this->parameter,
                'param' => 'value',
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ParameterView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('parameter', $this->view::getIdentifier());
    }
}
