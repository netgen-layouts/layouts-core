<?php

namespace Netgen\BlockManager\Tests\Parameters\ParameterDefinition;

use Netgen\BlockManager\Parameters\ParameterDefinition\Html;
use PHPUnit\Framework\TestCase;

class HtmlTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Html::getType
     */
    public function testGetType()
    {
        $parameterDefinition = $this->getParameterDefinition();
        $this->assertEquals('html', $parameterDefinition->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Html::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Html::configureOptions
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
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Html::getOptions
     * @covers \Netgen\BlockManager\Parameters\ParameterDefinition\Html::configureOptions
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
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition\Html
     */
    public function getParameterDefinition($options = array())
    {
        return new Html($options);
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
