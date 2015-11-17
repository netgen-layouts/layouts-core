<?php

namespace Netgen\BlockManager\BlockDefinition\Tests\Parameters;

use Netgen\BlockManager\BlockDefinition\Parameters\TextArea;

class TextAreaTest extends ParameterTest
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\TextArea::getType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\TextArea::getFormType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\TextArea::configureOptions
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\TextArea::mapFormTypeOptions
     */
    public function testParameter()
    {
        $parameter = $this->getParameter(array());

        self::assertEquals('textarea', $parameter->getType());
        self::assertEquals('textarea', $parameter->getFormType());
        self::assertEquals(array(), $parameter->mapFormTypeOptions());
    }

    public function getParameter($attributes)
    {
        return new TextArea('test', 'Test', $attributes, 'Test value');
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
