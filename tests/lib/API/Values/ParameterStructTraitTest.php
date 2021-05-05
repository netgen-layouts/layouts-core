<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values;

use Netgen\Layouts\Parameters\CompoundParameterDefinition;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Tests\API\Stubs\ParameterStruct;
use Netgen\Layouts\Tests\Parameters\Stubs\ParameterCollection;
use Netgen\Layouts\Tests\Parameters\Stubs\ParameterDefinitionCollection;
use PHPUnit\Framework\TestCase;

final class ParameterStructTraitTest extends TestCase
{
    private ParameterStruct $struct;

    protected function setUp(): void
    {
        $this->struct = new ParameterStruct();
    }

    /**
     * @covers \Netgen\Layouts\API\Values\ParameterStructTrait::getParameterValues
     */
    public function testDefaultProperties(): void
    {
        self::assertSame([], $this->struct->getParameterValues());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\ParameterStructTrait::getParameterValues
     * @covers \Netgen\Layouts\API\Values\ParameterStructTrait::setParameterValues
     */
    public function testSetParameterValues(): void
    {
        $this->struct->setParameterValues(
            [
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ],
        );

        self::assertSame(
            [
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ],
            $this->struct->getParameterValues(),
        );
    }

    /**
     * @covers \Netgen\Layouts\API\Values\ParameterStructTrait::setParameterValue
     */
    public function testSetParameterValue(): void
    {
        $this->struct->setParameterValue('some_param', 'some_value');
        $this->struct->setParameterValue('some_other_param', 'some_other_value');

        self::assertSame(
            [
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ],
            $this->struct->getParameterValues(),
        );
    }

    /**
     * @covers \Netgen\Layouts\API\Values\ParameterStructTrait::setParameterValue
     */
    public function testOverwriteParameterValues(): void
    {
        $this->struct->setParameterValue('some_param', 'some_value');
        $this->struct->setParameterValue('some_param', 'new_value');

        self::assertSame(['some_param' => 'new_value'], $this->struct->getParameterValues());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\ParameterStructTrait::getParameterValue
     */
    public function testGetParameterValue(): void
    {
        $this->struct->setParameterValue('some_param', 'some_value');

        self::assertSame('some_value', $this->struct->getParameterValue('some_param'));
    }

    /**
     * @covers \Netgen\Layouts\API\Values\ParameterStructTrait::getParameterValue
     */
    public function testGetParameterValueWithNonExistingParameter(): void
    {
        self::assertNull($this->struct->getParameterValue('some_other_param'));
    }

    /**
     * @covers \Netgen\Layouts\API\Values\ParameterStructTrait::hasParameterValue
     */
    public function testHasParameterValue(): void
    {
        $this->struct->setParameterValue('some_param', 'some_value');

        self::assertTrue($this->struct->hasParameterValue('some_param'));
    }

    /**
     * @covers \Netgen\Layouts\API\Values\ParameterStructTrait::hasParameterValue
     */
    public function testHasParameterValueWithNoValue(): void
    {
        $this->struct->setParameterValue('some_param', 'some_value');

        self::assertFalse($this->struct->hasParameterValue('some_other_param'));
    }

    /**
     * @covers \Netgen\Layouts\API\Values\ParameterStructTrait::fillDefault
     */
    public function testFillDefault(): void
    {
        $parameterDefinitions = $this->buildParameterDefinitionCollection();

        $this->struct->fillDefaultParameters($parameterDefinitions);

        self::assertSame(
            [
                'css_class' => 'css_default',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner_default',
            ],
            $this->struct->getParameterValues(),
        );
    }

    /**
     * @covers \Netgen\Layouts\API\Values\ParameterStructTrait::fillFromCollection
     */
    public function testFillFromCollection(): void
    {
        $parameterDefinitions = $this->buildParameterDefinitionCollection();

        /** @var \Netgen\Layouts\Parameters\CompoundParameterDefinition $compoundParameter */
        $compoundParameter = $parameterDefinitions->getParameterDefinition('compound');

        $parameters = ParameterCollection::fromArray(
            [
                'parameters' => [
                    'css_class' => Parameter::fromArray(
                        [
                            'value' => 'css',
                            'parameterDefinition' => $parameterDefinitions->getParameterDefinition('css_class'),
                        ],
                    ),
                    'inner' => Parameter::fromArray(
                        [
                            'value' => 'inner',
                            'parameterDefinition' => $compoundParameter->getParameterDefinition('inner'),
                        ],
                    ),
                ],
            ],
        );

        $this->struct->fillParametersFromCollection($parameterDefinitions, $parameters);

        self::assertSame(
            [
                'css_class' => 'css',
                'css_id' => null,
                'compound' => null,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues(),
        );
    }

    /**
     * @covers \Netgen\Layouts\API\Values\ParameterStructTrait::fillFromHash
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

        self::assertSame(
            [
                'css_class' => 'css',
                'css_id' => 'id',
                'compound' => false,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues(),
        );
    }

    /**
     * @covers \Netgen\Layouts\API\Values\ParameterStructTrait::fillFromHash
     */
    public function testFillFromHashWithMissingValues(): void
    {
        $parameterDefinitions = $this->buildParameterDefinitionCollection();

        $initialValues = [
            'css_class' => 'css',
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($parameterDefinitions, $initialValues);

        self::assertSame(
            [
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues(),
        );
    }

    /**
     * @covers \Netgen\Layouts\API\Values\ParameterStructTrait::fillFromHash
     */
    public function testFillFromHashWithImport(): void
    {
        $parameterDefinitions = $this->buildParameterDefinitionCollection();

        $initialValues = [
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($parameterDefinitions, $initialValues, true);

        self::assertSame(
            [
                'css_class' => 'css',
                'css_id' => 'id',
                'compound' => false,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues(),
        );
    }

    /**
     * @covers \Netgen\Layouts\API\Values\ParameterStructTrait::fillFromHash
     */
    public function testFillFromHashWithImportAndMissingValues(): void
    {
        $parameterDefinitions = $this->buildParameterDefinitionCollection();

        $initialValues = [
            'css_class' => 'css',
            'inner' => 'inner',
        ];

        $this->struct->fillParametersFromHash($parameterDefinitions, $initialValues, true);

        self::assertSame(
            [
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ],
            $this->struct->getParameterValues(),
        );
    }

    private function buildParameterDefinitionCollection(): ParameterDefinitionCollectionInterface
    {
        $compoundParameter = CompoundParameterDefinition::fromArray(
            [
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'isRequired' => false,
                'defaultValue' => true,
                'parameterDefinitions' => [
                    'inner' => ParameterDefinition::fromArray(
                        [
                            'name' => 'inner',
                            'type' => new ParameterType\TextLineType(),
                            'isRequired' => false,
                            'defaultValue' => 'inner_default',
                        ],
                    ),
                ],
            ],
        );

        $parameterDefinitions = [
            'css_class' => ParameterDefinition::fromArray(
                [
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'isRequired' => false,
                    'defaultValue' => 'css_default',
                ],
            ),
            'css_id' => ParameterDefinition::fromArray(
                [
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'isRequired' => false,
                    'defaultValue' => 'id_default',
                ],
            ),
            'compound' => $compoundParameter,
        ];

        return new ParameterDefinitionCollection($parameterDefinitions);
    }
}
