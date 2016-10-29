<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterDefinition;

use Netgen\BlockManager\Parameters\ParameterDefinition\TextLine;
use PHPUnit\Framework\TestCase;

class TextLineTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\TextLine::getType
     */
    public function testGetType()
    {
        $parameterDefinition = $this->getParameterDefinition();
        $this->assertEquals('text_line', $parameterDefinition->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\TextLine::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\TextLine::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\TextLine::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\TextLine::configureOptions
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
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition\TextLine
     */
    public function getParameterDefinition($options = array())
    {
        return new TextLine($options);
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
