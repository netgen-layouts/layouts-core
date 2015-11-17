<?php

namespace Netgen\BlockManager\BlockDefinition\Tests\Parameters;

use Netgen\BlockManager\BlockDefinition\Parameters\Text;

class TextTest extends ParameterTest
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\Text::getType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\Text::getFormType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\Text::configureOptions
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\Text::mapFormTypeOptions
     */
    public function testParameter()
    {
        $parameter = $this->getParameter(array());

        self::assertEquals('text', $parameter->getType());
        self::assertEquals('text', $parameter->getFormType());
        self::assertEquals(array(), $parameter->mapFormTypeOptions());
    }

    public function getParameter($attributes)
    {
        return new Text('test', 'Test', $attributes, 'Test value');
    }

    /**
     * Provider for testing valid parameter attributes
     *
     * @return array
     */
    public function validAttributesProvider()
    {
        return array(
            array(
                array(),
                array(),
            ),
        );
    }

    /**
     * Provider for testing invalid parameter attributes
     *
     * @return array
     */
    public function invalidAttributesProvider()
    {
        return array(array(null));
    }
}
