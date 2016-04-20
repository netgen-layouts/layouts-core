<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Parameter;

abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
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
    abstract public function getParameter($options);

    /**
     * Provider for testing valid parameter attributes.
     *
     * @return array
     */
    abstract public function validOptionsProvider();

    /**
     * Provider for testing invalid parameter attributes.
     *
     * @return array
     */
    abstract public function invalidOptionsProvider();
}
