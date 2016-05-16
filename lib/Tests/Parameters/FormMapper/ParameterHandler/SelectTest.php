<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Select;
use Netgen\BlockManager\Parameters\Parameter\Select as SelectParameter;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Hidden
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new Select();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Select::getFormType
     */
    public function testGetFormType()
    {
        self::assertEquals('choice', $this->handler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Select::convertOptions
     */
    public function testConvertOptions()
    {
        $parameter = new SelectParameter(
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
            $this->handler->convertOptions($parameter)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Select::convertOptions
     */
    public function testConvertOptionsWithClosure()
    {
        $parameter = new SelectParameter(
            array(
                'options' => function() {
                    return array(
                        'Heading 1' => 'h1',
                    );
                },
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
            $this->handler->convertOptions($parameter)
        );
    }
}
