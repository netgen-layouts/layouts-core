<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Parameter;

use Netgen\BlockManager\BlockDefinition\Parameter\Hidden;

class HiddenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Hidden::getFormType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Hidden::configureOptions
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Hidden::mapFormTypeOptions
     */
    public function testParameter()
    {
        $parameter = $this->getParameter(array());

        self::assertEquals(array(), $parameter->mapFormTypeOptions());
        self::assertEquals(
            'hidden',
            $parameter->getFormType()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Hidden::getOptions
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Hidden::configureOptions
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
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Hidden::getOptions
     * @covers \Netgen\BlockManager\BlockDefinition\Parameter\Hidden::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     *
     * @param array $options
     */
    public function testInvalidOptions($options)
    {
        if ($options === null) {
            $this->markTestSkipped('This parameter has no invalid values.');
        }

        $parameter = $this->getParameter($options);
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     *
     * @return \Netgen\BlockManager\BlockDefinition\Parameter\Hidden
     */
    public function getParameter($options)
    {
        return new Hidden('Test value', $options);
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
