<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Form\KeyValuesType;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\RouteParameterMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

#[CoversClass(RouteParameterMapper::class)]
final class RouteParameterMapperTest extends TestCase
{
    private RouteParameterMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new RouteParameterMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(KeyValuesType::class, $this->mapper->getFormType());
    }

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
            $this->mapper->getFormOptions(),
        );
    }
}
