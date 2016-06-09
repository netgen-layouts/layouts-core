<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\Core\Values\QueryUpdateStruct;
use PHPUnit\Framework\TestCase;

class QueryUpdateStructTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryUpdateStruct::__construct
     */
    public function testDefaultProperties()
    {
        $queryUpdateStruct = new QueryUpdateStruct();

        self::assertNull($queryUpdateStruct->identifier);
        self::assertEquals(array(), $queryUpdateStruct->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryUpdateStruct::__construct
     * @covers \Netgen\BlockManager\Core\Values\QueryUpdateStruct::getParameters
     */
    public function testSetProperties()
    {
        $queryUpdateStruct = new QueryUpdateStruct(
            array(
                'identifier' => 'my_query',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
            )
        );

        self::assertEquals('my_query', $queryUpdateStruct->identifier);
        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $queryUpdateStruct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryUpdateStruct::setParameters
     */
    public function testSetParameters()
    {
        $queryUpdateStruct = new QueryUpdateStruct();
        $queryUpdateStruct->setParameters(
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
            $queryUpdateStruct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryUpdateStruct::setParameter
     */
    public function testSetParameter()
    {
        $queryUpdateStruct = new QueryUpdateStruct();
        $queryUpdateStruct->setParameter('some_param', 'some_value');
        $queryUpdateStruct->setParameter('some_other_param', 'some_other_value');

        self::assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $queryUpdateStruct->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryUpdateStruct::setParameter
     */
    public function testOverwriteParameters()
    {
        $queryUpdateStruct = new QueryUpdateStruct();
        $queryUpdateStruct->setParameter('some_param', 'some_value');
        $queryUpdateStruct->setParameter('some_param', 'new_value');

        self::assertEquals(array('some_param' => 'new_value'), $queryUpdateStruct->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryUpdateStruct::getParameter
     */
    public function testGetParameter()
    {
        $queryUpdateStruct = new QueryUpdateStruct();
        $queryUpdateStruct->setParameter('some_param', 'some_value');

        self::assertEquals('some_value', $queryUpdateStruct->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryUpdateStruct::getParameter
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetParameterThrowsInvalidArgumentException()
    {
        $queryUpdateStruct = new QueryUpdateStruct();
        $queryUpdateStruct->setParameter('some_param', 'some_value');

        $queryUpdateStruct->getParameter('some_other_param');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryUpdateStruct::hasParameter
     */
    public function testHasParameter()
    {
        $queryUpdateStruct = new QueryUpdateStruct();
        $queryUpdateStruct->setParameter('some_param', 'some_value');

        self::assertTrue($queryUpdateStruct->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\QueryUpdateStruct::hasParameter
     */
    public function testHasParameterWithNoParameter()
    {
        $queryUpdateStruct = new QueryUpdateStruct();
        $queryUpdateStruct->setParameter('some_param', 'some_value');

        self::assertFalse($queryUpdateStruct->hasParameter('some_other_param'));
    }
}
