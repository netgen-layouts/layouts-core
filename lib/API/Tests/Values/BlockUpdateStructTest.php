<?php

namespace Netgen\BlockManager\API\Tests\Values;

use Netgen\BlockManager\Core\Values\BlockUpdateStruct;
use PHPUnit_Framework_TestCase;

class BlockUpdateStructTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\BlockUpdateStruct::__construct
     * @covers \Netgen\BlockManager\API\Values\BlockUpdateStruct::setParameter
     * @covers \Netgen\BlockManager\API\Values\BlockUpdateStruct::getParameters
     */
    public function testSetProperties()
    {
        $blockCreateStruct = new BlockUpdateStruct(
            array(
                'viewType' => 'default',
            )
        );

        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_other_param', 'some_other_value');

        self::assertEquals('default', $blockCreateStruct->viewType);
        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $blockCreateStruct->getParameters()
        );
    }
}
