<?php

namespace Netgen\BlockManager\API\Tests\Values;

use Netgen\BlockManager\Core\Values\BlockCreateStruct;
use PHPUnit_Framework_TestCase;

class BlockCreateStructTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\BlockCreateStruct::__construct
     * @covers \Netgen\BlockManager\Core\Values\BlockCreateStruct::getParameters
     */
    public function testSetProperties()
    {
        $blockCreateStruct = new BlockCreateStruct(
            array(
                'definitionIdentifier' => 'paragraph',
                'viewType' => 'default',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
            )
        );

        self::assertEquals('paragraph', $blockCreateStruct->definitionIdentifier);
        self::assertEquals('default', $blockCreateStruct->viewType);
        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $blockCreateStruct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\BlockCreateStruct::setParameters
     */
    public function testSetParameters()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->setParameters(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            )
        );

        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $blockCreateStruct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\BlockCreateStruct::setParameter
     */
    public function testSetParameter()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_other_param', 'some_other_value');

        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $blockCreateStruct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\BlockCreateStruct::setParameter
     */
    public function testOverwriteParameters()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_param', 'new_value');

        self::assertEquals(array('some_param' => 'new_value'), $blockCreateStruct->getParameters());
    }
}
