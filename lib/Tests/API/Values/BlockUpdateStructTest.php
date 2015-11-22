<?php

namespace Netgen\BlockManager\Tests\API\Values;

use Netgen\BlockManager\Core\Values\BlockUpdateStruct;

class BlockUpdateStructTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\BlockUpdateStruct::__construct
     */
    public function testDefaultProperties()
    {
        $blockUpdateStruct = new BlockUpdateStruct();

        self::assertNull($blockUpdateStruct->viewType);
        self::assertEquals(array(), $blockUpdateStruct->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\BlockUpdateStruct::__construct
     * @covers \Netgen\BlockManager\Core\Values\BlockUpdateStruct::getParameters
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
     * @covers \Netgen\BlockManager\Core\Values\BlockUpdateStruct::setParameters
     */
    public function testSetParameters()
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->setParameters(
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
            $blockUpdateStruct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\BlockUpdateStruct::setParameter
     */
    public function testSetParameter()
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->setParameter('some_param', 'some_value');
        $blockUpdateStruct->setParameter('some_other_param', 'some_other_value');

        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $blockUpdateStruct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\BlockUpdateStruct::setParameter
     */
    public function testOverwriteParameters()
    {
        $blockUpdateStruct = new BlockUpdateStruct();
        $blockUpdateStruct->setParameter('some_param', 'some_value');
        $blockUpdateStruct->setParameter('some_param', 'new_value');

        self::assertEquals(array('some_param' => 'new_value'), $blockUpdateStruct->getParameters());
    }
}
