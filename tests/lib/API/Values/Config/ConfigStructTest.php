<?php

namespace Netgen\BlockManager\Tests\API\Values\Block;

use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionWithParameterDefinitions;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameterDefinition;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinition;
use PHPUnit\Framework\TestCase;

final class ConfigStructTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Config\ConfigStruct
     */
    private $struct;

    public function setUp()
    {
        $this->struct = new ConfigStruct();
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigStruct::fillParameters
     */
    public function testFillParameters()
    {
        $configDefinition = $this->buildConfigDefinition();

        $initialValues = array(
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        );

        $this->struct->fillParameters($configDefinition, $initialValues);

        $this->assertEquals(
            array(
                'css_class' => 'css',
                'css_id' => 'id',
                'compound' => false,
                'inner' => 'inner',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigStruct::fillParameters
     */
    public function testFillParametersWithMissingValues()
    {
        $configDefinition = $this->buildConfigDefinition();

        $initialValues = array(
            'css_class' => 'css',
            'inner' => 'inner',
        );

        $this->struct->fillParameters($configDefinition, $initialValues);

        $this->assertEquals(
            array(
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigStruct::fillParametersFromConfig
     */
    public function testFillParametersFromConfig()
    {
        $configDefinition = $this->buildConfigDefinition();

        $config = new Config(
            array(
                'definition' => $configDefinition,
                'parameters' => array(
                    'css_class' => new Parameter(
                        array(
                            'value' => 'css',
                            'parameterDefinition' => $configDefinition->getParameterDefinition('css_class'),
                        )
                    ),
                    'inner' => new Parameter(
                        array(
                            'value' => 'inner',
                            'parameterDefinition' => $configDefinition->getParameterDefinition('compound')->getParameterDefinition('inner'),
                        )
                    ),
                ),
            )
        );

        $this->struct->fillParametersFromConfig($config);

        $this->assertEquals(
            array(
                'css_class' => 'css',
                'css_id' => null,
                'compound' => null,
                'inner' => 'inner',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigStruct::fillParametersFromHash
     */
    public function testFillParametersFromHash()
    {
        $configDefinition = $this->buildConfigDefinition();

        $initialValues = array(
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        );

        $this->struct->fillParametersFromHash($configDefinition, $initialValues);

        $this->assertEquals(
            array(
                'css_class' => 'css',
                'css_id' => 'id',
                'compound' => false,
                'inner' => 'inner',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigStruct::fillParametersFromHash
     */
    public function testFillParametersFromHashWithMissingValues()
    {
        $configDefinition = $this->buildConfigDefinition();

        $initialValues = array(
            'css_class' => 'css',
            'inner' => 'inner',
        );

        $this->struct->fillParametersFromHash($configDefinition, $initialValues);

        $this->assertEquals(
            array(
                'css_class' => 'css',
                'css_id' => 'id_default',
                'compound' => true,
                'inner' => 'inner',
            ),
            $this->struct->getParameterValues()
        );
    }

    /**
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    private function buildConfigDefinition()
    {
        $compoundParameter = new CompoundParameterDefinition(
            array(
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'defaultValue' => true,
            )
        );

        $compoundParameter->setParameterDefinitions(
            array(
                'inner' => new ParameterDefinition(
                    array(
                        'name' => 'inner',
                        'type' => new ParameterType\TextLineType(),
                        'defaultValue' => 'inner_default',
                    )
                ),
            )
        );

        $parameterDefinitions = array(
            'css_class' => new ParameterDefinition(
                array(
                    'name' => 'css_class',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'css_default',
                )
            ),
            'css_id' => new ParameterDefinition(
                array(
                    'name' => 'css_id',
                    'type' => new ParameterType\TextLineType(),
                    'defaultValue' => 'id_default',
                )
            ),
            'compound' => $compoundParameter,
        );

        return new ConfigDefinitionWithParameterDefinitions($parameterDefinitions);
    }
}
