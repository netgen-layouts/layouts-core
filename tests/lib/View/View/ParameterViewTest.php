<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Tests\Parameters\Stubs\ParameterType;
use Netgen\Layouts\View\View\ParameterView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParameterView::class)]
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
                        'defaultValue' => null,
                    ],
                ),
            ],
        );

        $this->view = new ParameterView($this->parameter);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('parameter', 42);
    }

    public function testGetParameter(): void
    {
        self::assertSame($this->parameter, $this->view->getParameterValue());
    }

    public function testGetParameters(): void
    {
        self::assertSame(
            [
                'param' => 'value',
                'parameter' => $this->parameter,
            ],
            $this->view->getParameters(),
        );
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('parameter', $this->view::getIdentifier());
    }
}
