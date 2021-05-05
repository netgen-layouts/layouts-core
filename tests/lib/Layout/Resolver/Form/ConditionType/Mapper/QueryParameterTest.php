<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Form\KeyValuesType;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\QueryParameter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class QueryParameterTest extends TestCase
{
    private QueryParameter $mapper;

    protected function setUp(): void
    {
        $this->mapper = new QueryParameter();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\QueryParameter::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(KeyValuesType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper\QueryParameter::getFormOptions
     */
    public function testGetFormOptions(): void
    {
        self::assertSame(
            [
                'label' => false,
                'required' => false,
                'key_name' => 'parameter_name',
                'key_label' => 'condition_type.query_parameter.parameter_name',
                'values_name' => 'parameter_values',
                'values_label' => 'condition_type.query_parameter.parameter_values',
                'values_type' => TextType::class,
                'values_options' => [
                    'empty_data' => ' ',
                ],
            ],
            $this->mapper->getFormOptions(),
        );
    }
}
