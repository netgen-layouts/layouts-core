<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Core\Stubs\ParameterStruct;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterCollection;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinitionCollection;
use PHPUnit\Framework\TestCase;

final class ParameterStructTraitTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Tests\Core\Stubs\ParameterStruct
     */
    private $struct;

    public function setUp(): void
    {
        $this->struct = new ParameterStruct();
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::getParameterValues
     */
    public function testDefaultProperties(): void
    {
        $this->assertSame([], $this->struct->getParameterValues());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::getParameterValues
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::setParameterValues
     */
    public function testSetParameterValues(): void
    {
        $this->struct->setParameterValues(
            [
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ]
        );

        $this->assertSame(
            [
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::setParameterValue
     */
    public function testSetParameterValue(): void
    {
        $this->struct->setParameterValue('some_param', 'some_value');
        $this->struct->setParameterValue('some_other_param', 'some_other_value');

        $this->assertSame(
            [
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::setParameterValue
     */
    public function testOverwriteParameterValues(): void
    {
        $this->struct->setParameterValue('some_param', 'some_value');
        $this->struct->setParameterValue('some_param', 'new_value');

        $this->assertSame(['some_param' => 'new_value'], $this->struct->getParameterValues());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::getParameterValue
     */
    public function testGetParameterValue(): void
    {
        $this->struct->setParameterValue('some_param', 'some_value');

        $this->assertSame('some_value', $this->struct->getParameterValue('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::getParameterValue
     */
    public function testGetParameterValueWithNonExistingParameter(): void
    {
        $this->assertNull($this->struct->getParameterValue('some_other_param'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::hasParameterValue
     */
    public function testHasParameterValue(): void
    {
        $this->struct->setParameterValue('some_param', 'some_value');

        $this->assertTrue($this->struct->hasParameterValue('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::hasParameterValue
     */
    public function testHasParameterValueWithNoValue(): void
    {
        $this->struct->setParameterValue('some_param', 'some_value');

        $this->assertFalse($this->struct->hasParameterValue('some_other_param'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::fillDefault
     */
    public function testFillDefault(): void
    {
        $parameterDefinitions = $this->buildParameterDefinitionCollection();

        $this->struct->fillDefaultParameters($parameterDefinitions);

        $this->assertSame(
            [
                'css_class' => 'css_default',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner_default',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::fillFromCollection
     */
    public function testFillFromCollection(): void
    {
        $parameterDefinitions = $this->buildParameterDefinitionCollection();

        /** @var \Netgen\BlockManager\Parameters\CompoundParameterDefinition $compoundParameter */
        $compoundParameter = $parameterDefinitions->getParameterDefinition('compound');

        $parameters = new ParameterCollection(
            [
                'parameters' => [
                    'css_class' => new Parameter(
                        [
                            'value' => 'css',
                            'parameterDefinition' => $parameterDefinitions->getParameterDefinition('css_class'),
                        ]
                    ),
                    'inner' => new Parameter(
                        [
                            'value' => 'inner',
                            'parameterDefinition' => $compoundParameter->getParameterDefinition('inner'),
                        ]
                    ),
                ],
            ]
        );

        $this->struct->fillParametersFromCollection($parameterDefinitions, $parameters);

        $this->assertSame(
            [
                'css_class' => 'css',
                'css_id' => null,
                'compound' => null,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::fillFromHash
     */
    public function testFillFromHash(): void
    {
        $parameterDefinitions = $this->buildParameterDefinitionCollection();

        $initialValues = [
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($parameterDefinitions, $initialValues);

        $this->assertSame(
            [
                'css_class' => 'css',
                'css_id' => 'id',
                'compound' => false,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\ParameterStructTrait::fillFromHash
     */
    public function testFillFromHashWithMissingValues(): void
    {
        $parameterDefinitions = $this->buildParameterDefinitionCollection();

        $initialValues = [
            'css_class' => 'css',
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($parameterDefinitions, $initialValues);

        $this->assertSame(
            [
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues()
        );
    }

    private function buildParameterDefinitionCollection(): ParameterDefinitionCollectionInterface
    {
        $compoundParameter = new CompoundParameterDefinition(
            [
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'defaultValue' => true,
                'parameterDefinitions' => [
                    'inner' => new ParameterDefinition(
                        [
                            'name' => 'inner',
                            'type' => new ParameterType\TextLineType(),
                            'defaultValue' => 'inner_default',
                        ]
                    ),
                ],
            ]
        );

        $parameterDefinitions = [
            'css_class' => new ParameterDefinition(
                [
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'css_default',
                ]
            ),
            'css_id' => new ParameterDefinition(
                [
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'id_default',
                ]
            ),
            'compound' => $compoundParameter,
        ];

        return new ParameterDefinitionCollection($parameterDefinitions);
    }
}
