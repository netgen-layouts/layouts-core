<?php

namespace Netgen\BlockManager\Tests\API\Values\Block;

use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use PHPUnit\Framework\TestCase;

final class BlockCreateStructTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Block\BlockCreateStruct
     */
    private $struct;

    public function setUp()
    {
        $this->struct = new BlockCreateStruct(
            array(
                'collectionCreateStructs' => array(
                    'default' => new CollectionCreateStruct(array('offset' => 0)),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::getCollectionCreateStructs
     */
    public function testGetCollectionCreateStructs()
    {
        $this->assertEquals(
            array('default' => new CollectionCreateStruct(array('offset' => 0))),
            $this->struct->getCollectionCreateStructs()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::addCollectionCreateStruct
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::getCollectionCreateStructs
     */
    public function testAddCollectionCreateStruct()
    {
        $this->struct->addCollectionCreateStruct('default', new CollectionCreateStruct(array('offset' => 5)));
        $this->struct->addCollectionCreateStruct('featured', new CollectionCreateStruct(array('offset' => 10)));

        $this->assertEquals(
            array(
                'default' => new CollectionCreateStruct(array('offset' => 5)),
                'featured' => new CollectionCreateStruct(array('offset' => 10)),
            ),
            $this->struct->getCollectionCreateStructs()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::fillParameters
     */
    public function testFillParameters()
    {
        $blockDefinition = $this->buildBlockDefinition();

        $initialValues = array(
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        );

        $this->struct->fillParameters($blockDefinition, $initialValues);

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
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::fillParameters
     */
    public function testFillParametersWithMissingValues()
    {
        $blockDefinition = $this->buildBlockDefinition();

        $initialValues = array(
            'css_class' => 'css',
            'inner' => 'inner',
        );

        $this->struct->fillParameters($blockDefinition, $initialValues);

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
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::fillParametersFromBlock
     */
    public function testFillParametersFromBlock()
    {
        $blockDefinition = $this->buildBlockDefinition();

        $block = new Block(
            array(
                'definition' => $blockDefinition,
                'parameters' => array(
                    'css_class' => new Parameter(
                        array(
                            'value' => 'css',
                            'parameterDefinition' => $blockDefinition->getParameterDefinition('css_class'),
                        )
                    ),
                    'inner' => new Parameter(
                        array(
                            'value' => 'inner',
                            'parameterDefinition' => $blockDefinition->getParameterDefinition('compound')->getParameterDefinition('inner'),
                        )
                    ),
                ),
            )
        );

        $this->struct->fillParametersFromBlock($block);

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
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::fillParametersFromHash
     */
    public function testFillParametersFromHash()
    {
        $blockDefinition = $this->buildBlockDefinition();

        $initialValues = array(
            'css_class' => 'css',
            'css_id' => 'id',
            'compound' => false,
            'inner' => 'inner',
        );

        $this->struct->fillParametersFromHash($blockDefinition, $initialValues);

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
     * @covers \Netgen\BlockManager\API\Values\Block\BlockCreateStruct::fillParametersFromHash
     */
    public function testFillParametersFromHashWithMissingValues()
    {
        $blockDefinition = $this->buildBlockDefinition();

        $initialValues = array(
            'css_class' => 'css',
            'inner' => 'inner',
        );

        $this->struct->fillParametersFromHash($blockDefinition, $initialValues);

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
     * @return \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    private function buildBlockDefinition()
    {
        $compoundParameter = new CompoundParameterDefinition(
            array(
                'name' => 'compound',
                'type' => new ParameterType\Compound\BooleanType(),
                'defaultValue' => true,
                'parameterDefinitions' => array(
                    'inner' => new ParameterDefinition(
                        array(
                            'name' => 'inner',
                            'type' => new ParameterType\TextLineType(),
                            'defaultValue' => 'inner_default',
                        )
                    ),
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

        return new BlockDefinition(array('parameterDefinitions' => $parameterDefinitions));
    }
}
