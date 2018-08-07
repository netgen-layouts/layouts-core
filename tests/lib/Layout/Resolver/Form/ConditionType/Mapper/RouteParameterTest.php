<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Form\KeyValuesType;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\RouteParameter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class RouteParameterTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new RouteParameter();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\RouteParameter::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(KeyValuesType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper\RouteParameter::getFormOptions
     */
    public function testGetFormOptions(): void
    {
        self::assertSame(
            [
                'label' => false,
                'required' => false,
                'key_name' => 'parameter_name',
                'key_label' => 'condition_type.route_parameter.parameter_name',
                'values_name' => 'parameter_values',
                'values_label' => 'condition_type.route_parameter.parameter_values',
                'values_type' => TextType::class,
                'values_options' => [
                    'empty_data' => ' ',
                ],
            ],
            $this->mapper->getFormOptions()
        );
    }
}
