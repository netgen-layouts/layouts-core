<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Parameters\Parameter\Text;
use Netgen\BlockManager\Tests\Parameters\Stubs\CompoundParameter;
use stdClass;
use PHPUnit\Framework\TestCase;

class CompoundParameterTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::__construct
     * @expectedException \LogicException
     */
    public function testConstructorWithNonParameterObject()
    {
        new CompoundParameter(array('param' => new stdClass()), array(), true);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::__construct
     * @expectedException \LogicException
     */
    public function testConstructorWithCompoundParameters()
    {
        new CompoundParameter(array('param' => new CompoundParameter()), array(), true);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::__construct
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::getParameters
     */
    public function testGetParameters()
    {
        $parameter = new CompoundParameter(array('param' => new Text()), array(), true);

        self::assertEquals(array('param' => new Text()), $parameter->getParameters());
    }
}
