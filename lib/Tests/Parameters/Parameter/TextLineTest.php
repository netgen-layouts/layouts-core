<?php

namespace Netgen\BlockManager\Tests\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter\TextLine;
use PHPUnit\Framework\TestCase;

class TextLineTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\TextLine::getType
     */
    public function testGetType()
    {
        $parameter = $this->getParameter(array());
        self::assertEquals('text_line', $parameter->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\TextLine::getOptions
     * @covers \Netgen\BlockManager\Parameters\Parameter\TextLine::configureOptions
     * @dataProvider validOptionsProvider
     *
     * @param array $options
     * @param array $resolvedOptions
     */
    public function testValidOptions($options, $resolvedOptions)
    {
        $parameter = $this->getParameter($options);
        self::assertEquals($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Parameter\TextLine::getOptions
     * @covers \Netgen\BlockManager\Parameters\Parameter\TextLine::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     *
     * @param array $options
     */
    public function testInvalidOptions($options)
    {
        $this->getParameter($options);
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     *
     * @return \Netgen\BlockManager\Parameters\Parameter\TextLine
     */
    public function getParameter($options)
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
