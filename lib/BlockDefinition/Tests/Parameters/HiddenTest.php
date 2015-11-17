<?php

namespace Netgen\BlockManager\BlockDefinition\Tests\Parameters;

use Netgen\BlockManager\BlockDefinition\Parameters\Hidden;

class HiddenTest extends ParameterTest
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\Hidden::getType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\Hidden::getFormType
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\Hidden::configureOptions
     * @covers \Netgen\BlockManager\BlockDefinition\Parameters\Hidden::mapFormTypeOptions
     */
    public function testParameter()
    {
        $parameter = $this->getParameter(array());

        self::assertEquals('hidden', $parameter->getType());
        self::assertEquals('hidden', $parameter->getFormType());
        self::assertEquals(array(), $parameter->mapFormTypeOptions());
    }

    public function getParameter($attributes)
    {
        return new Hidden('test', 'Test', $attributes, 'Test value');
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
