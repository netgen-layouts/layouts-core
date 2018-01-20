<?php

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;
use Netgen\BlockManager\Parameters\CompoundParameter;
use PHPUnit\Framework\TestCase;

final class CompoundParameterTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::__construct
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::getParameter
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::getParameters
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::hasParameter
     */
    public function testDefaultProperties()
    {
        $parameter = new CompoundParameter();

        $this->assertEquals(array(), $parameter->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::getParameter
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::getParameters
     * @covers \Netgen\BlockManager\Parameters\CompoundParameter::hasParameter
     */
    public function testSetProperties()
    {
        $parameter = new CompoundParameter(
            array(
                'parameters' => array('name' => 'value'),
            )
        );

        $this->assertEquals(array('name' => 'value'), $parameter->getParameters());

        $this->assertFalse($parameter->hasParameter('test'));
        $this->assertTrue($parameter->hasParameter('name'));

        try {
            $this->assertEquals(array(), $parameter->getParameter('test'));
            $this->fail('Fetched a parameter in empty collection.');
        } catch (ParameterException $e) {
            // Do nothing
        }

        $this->assertEquals('value', $parameter->getParameter('name'));
    }
}
