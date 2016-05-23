<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\Core\Values\BlockCreateStruct;

class BlockCreateStructTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\BlockCreateStruct::__construct
     */
    public function testDefaultProperties()
    {
        $blockCreateStruct = new BlockCreateStruct();

        self::assertNull($blockCreateStruct->definitionIdentifier);
        self::assertNull($blockCreateStruct->viewType);
        self::assertNull($blockCreateStruct->name);
        self::assertEquals(array(), $blockCreateStruct->getParameters());
    }

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
                'name' => 'My block',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
            )
        );

        self::assertEquals('paragraph', $blockCreateStruct->definitionIdentifier);
        self::assertEquals('default', $blockCreateStruct->viewType);
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

    /**
     * @covers \Netgen\BlockManager\Core\Values\BlockCreateStruct::getParameter
     */
    public function testGetParameter()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->setParameter('some_param', 'some_value');

        self::assertEquals('some_value', $blockCreateStruct->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\BlockCreateStruct::getParameter
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetParameterThrowsInvalidArgumentException()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->setParameter('some_param', 'some_value');

        $blockCreateStruct->getParameter('some_other_param');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\BlockCreateStruct::hasParameter
     */
    public function testHasParameter()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->setParameter('some_param', 'some_value');

        self::assertTrue($blockCreateStruct->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\BlockCreateStruct::hasParameter
     */
    public function testHasParameterWithNoParameter()
    {
        $blockCreateStruct = new BlockCreateStruct();
        $blockCreateStruct->setParameter('some_param', 'some_value');

        self::assertFalse($blockCreateStruct->hasParameter('some_other_param'));
    }
}
