<?php

namespace Netgen\BlockManager\Tests\API\Values;

use Netgen\BlockManager\Core\Values\QueryCreateStruct;

class QueryCreateStructTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryCreateStruct::__construct
     */
    public function testDefaultProperties()
    {
        $queryCreateStruct = new QueryCreateStruct();

        self::assertNull($queryCreateStruct->identifier);
        self::assertNull($queryCreateStruct->type);
        self::assertEquals(array(), $queryCreateStruct->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryCreateStruct::__construct
     * @covers \Netgen\BlockManager\Core\Values\QueryCreateStruct::getParameters
     */
    public function testSetProperties()
    {
        $queryCreateStruct = new QueryCreateStruct(
            array(
                'identifier' => 'my_query',
                'type' => 'ezcontent_search',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
            )
        );

        self::assertEquals('my_query', $queryCreateStruct->identifier);
        self::assertEquals('ezcontent_search', $queryCreateStruct->type);
        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $queryCreateStruct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryCreateStruct::setParameters
     */
    public function testSetParameters()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->setParameters(
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
            $queryCreateStruct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryCreateStruct::setParameter
     */
    public function testSetParameter()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->setParameter('some_param', 'some_value');
        $queryCreateStruct->setParameter('some_other_param', 'some_other_value');

        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $queryCreateStruct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryCreateStruct::setParameter
     */
    public function testOverwriteParameters()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->setParameter('some_param', 'some_value');
        $queryCreateStruct->setParameter('some_param', 'new_value');

        self::assertEquals(array('some_param' => 'new_value'), $queryCreateStruct->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryCreateStruct::getParameter
     */
    public function testGetParameter()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->setParameter('some_param', 'some_value');

        self::assertEquals('some_value', $queryCreateStruct->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryCreateStruct::getParameter
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
     */
    public function testGetParameterThrowsInvalidArgumentException()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->setParameter('some_param', 'some_value');

        $queryCreateStruct->getParameter('some_other_param');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryCreateStruct::hasParameter
     */
    public function testHasParameter()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->setParameter('some_param', 'some_value');

        self::assertTrue($queryCreateStruct->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryCreateStruct::hasParameter
     */
    public function testHasParameterWithNoParameter()
    {
        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->setParameter('some_param', 'some_value');

        self::assertFalse($queryCreateStruct->hasParameter('some_other_param'));
    }
}
