<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Select;
use Netgen\BlockManager\Parameters\Parameter\Select as SelectParameter;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Select::getFormType
     */
    public function testGetFormType()
    {
        $handler = new Select();

        self::assertEquals('choice', $handler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Select::getFormType
     */
    public function testConvertOptions()
    {
        $handler = new Select();
        $parameter = new SelectParameter(
            null,
            false,
            array(
                'options' => array(
                    'Heading 1' => 'h1',
                ),
                'multiple' => false,
            )
        );

        self::assertEquals(
            array(
                'choices' => array(
                    'Heading 1' => 'h1',
                ),
                'multiple' => false,
                'choices_as_values' => true,
            ),
            $handler->convertOptions($parameter)
        );
    }
}
