<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\BlockCreateStruct;
use PHPUnit\Framework\TestCase;

class BlockCreateStructTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\BlockCreateStruct::__construct
     */
    public function testDefaultProperties()
    {
        $blockCreateStruct = new BlockCreateStruct();

        self::assertNull($blockCreateStruct->definitionIdentifier);
        self::assertNull($blockCreateStruct->viewType);
        self::assertNull($blockCreateStruct->itemViewType);
        self::assertNull($blockCreateStruct->name);
        self::assertEquals(array(), $blockCreateStruct->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\BlockCreateStruct::__construct
     * @covers \Netgen\BlockManager\API\Values\BlockCreateStruct::getParameters
     */
    public function testSetProperties()
    {
        $blockCreateStruct = new BlockCreateStruct(
            array(
                'definitionIdentifier' => 'text',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
            )
        );

        self::assertEquals('text', $blockCreateStruct->definitionIdentifier);
        self::assertEquals('default', $blockCreateStruct->viewType);
        self::assertEquals('standard', $blockCreateStruct->itemViewType);
        self::assertEquals('My block', $blockCreateStruct->name);
        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $blockCreateStruct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\BlockCreateStruct::setParameters
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
     * @covers \Netgen\BlockManager\API\Values\BlockCreateStruct::setParameter
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
     * @covers \Netgen\BlockManager\API\Values\BlockCreateStruct::setParameter
     */
    public function testOverwriteParameters()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->setParameter('some_param', 'some_value');
        $blockCreateStruct->setParameter('some_param', 'new_value');

        self::assertEquals(array('some_param' => 'new_value'), $blockCreateStruct->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\BlockCreateStruct::getParameter
     */
    public function testGetParameter()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->setParameter('some_param', 'some_value');

        self::assertEquals('some_value', $blockCreateStruct->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\BlockCreateStruct::getParameter
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetParameterThrowsInvalidArgumentException()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->setParameter('some_param', 'some_value');

        $blockCreateStruct->getParameter('some_other_param');
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\BlockCreateStruct::hasParameter
     */
    public function testHasParameter()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->setParameter('some_param', 'some_value');

        self::assertTrue($blockCreateStruct->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\BlockCreateStruct::hasParameter
     */
    public function testHasParameterWithNoParameter()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->setParameter('some_param', 'some_value');

        self::assertFalse($blockCreateStruct->hasParameter('some_other_param'));
    }
}
