<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterDefinition;

use Netgen\BlockManager\Parameters\ParameterDefinition\Text;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Text::getType
     */
    public function testGetType()
    {
        $parameterDefinition = $this->getParameterDefinition();
        $this->assertEquals('text', $parameterDefinition->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Text::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Text::configureOptions
     * @dataProvider validOptionsProvider
     *
     * @param array $options
     * @param array $resolvedOptions
     */
    public function testValidOptions($options, $resolvedOptions)
    {
        $parameterDefinition = $this->getParameterDefinition($options);
        $this->assertEquals($resolvedOptions, $parameterDefinition->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Text::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Text::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     *
     * @param array $options
     */
    public function testInvalidOptions($options)
    {
        $this->getParameterDefinition($options);
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition\Text
     */
    public function getParameterDefinition($options = array())
    {
        return new Text($options);
    }

    /**
     * Provider for testing valid parameter attributes.
     *
     * @return array
     */
    public function validOptionsProvider()
    {
        return array(
            array(
                array(),
                array(),
            ),
        );
    }

    /**
     * Provider for testing invalid parameter attributes.
     *
     * @return array
     */
    public function invalidOptionsProvider()
    {
        return array(
            array(
                array(
                    'undefined_value' => 'Value',
                ),
            ),
        );
    }
}
