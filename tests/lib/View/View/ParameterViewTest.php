<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Tests\Parameters\Stubs\ParameterType;
use Netgen\Layouts\View\View\ParameterView;
use PHPUnit\Framework\TestCase;

final class ParameterViewTest extends TestCase
{
    private Parameter $parameter;

    private ParameterView $view;

    protected function setUp(): void
    {
        $this->parameter = Parameter::fromArray(
            [
                'name' => 'paramName',
                'parameterDefinition' => ParameterDefinition::fromArray(
                    [
                        'type' => new ParameterType(),
                        'isRequired' => false,
                    ],
                ),
            ],
        );

        $this->view = new ParameterView($this->parameter);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('parameter', 42);
    }

    /**
     * @covers \Netgen\Layouts\View\View\ParameterView::__construct
     * @covers \Netgen\Layouts\View\View\ParameterView::getParameterValue
     */
    public function testGetParameter(): void
    {
        self::assertSame($this->parameter, $this->view->getParameterValue());
    }

    /**
     * @covers \Netgen\Layouts\View\View\ParameterView::getParameters
     */
    public function testGetParameters(): void
    {
        self::assertSame(
            [
                'parameter' => $this->parameter,
                'param' => 'value',
            ],
            $this->view->getParameters(),
        );
    }

    /**
     * @covers \Netgen\Layouts\View\View\ParameterView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('parameter', $this->view::getIdentifier());
    }
}
