<?php

namespace Netgen\BlockManager\API\Tests\Values;

use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use PHPUnit_Framework_TestCase;

class BlockUpdateStructTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\BlockUpdateStruct::__construct
     * @covers \Netgen\BlockManager\API\Values\BlockUpdateStruct::getParameters
     */
    public function testSetProperties()
    {
        $blockUpdateStruct = new BlockUpdateStruct(
            array(
                'viewType' => 'default',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
            )
        );

        self::assertEquals('default', $blockUpdateStruct->viewType);
        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $blockUpdateStruct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\BlockUpdateStruct::setParameters
     */
    public function testSetParameters()
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->setParameter('some_param', 'some_value');

        self::assertEquals(array('some_param' => 'some_value'), $blockUpdateStruct->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\BlockUpdateStruct::setParameters
     */
    public function testOverwriteParameters()
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->setParameter('some_param', 'some_value');
        $blockUpdateStruct->setParameter('some_param', 'new_value');

        self::assertEquals(array('some_param' => 'new_value'), $blockUpdateStruct->getParameters());
    }
}
