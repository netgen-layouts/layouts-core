<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Form\ParameterMapper\ParameterHandler;

use Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandler\Select;
use Netgen\BlockManager\BlockDefinition\Parameter\Select as SelectParameter;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandler\Select::getFormType
     */
    public function testGetFormType()
    {
        $handler = new Select();

        self::assertEquals('choice', $handler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandler\Select::getFormType
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
