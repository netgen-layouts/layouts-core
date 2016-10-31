<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\Parameter\Choice as ChoiceParameter;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\ChoiceHandler;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use PHPUnit\Framework\TestCase;

class ChoiceTestHandler extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\ChoiceHandler
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new ChoiceHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\ChoiceHandler::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ChoiceType::class, $this->parameterHandler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\ChoiceHandler::convertOptions
     */
    public function testConvertOptions()
    {
        $parameter = new ChoiceParameter(
            array(
                'multiple' => true,
                'options' => array(
                    'Option 1' => 'option1',
                    'Option 2' => 'option2',
                ),
            )
        );

        $this->assertEquals(
            array(
                'multiple' => true,
                'choices' => array(
                    'Option 1' => 'option1',
                    'Option 2' => 'option2',
                ),
                'choices_as_values' => true,
            ),
            $this->parameterHandler->convertOptions($parameter)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\ChoiceHandler::convertOptions
     */
    public function testConvertOptionsWithClosure()
    {
        $parameter = new ChoiceParameter(
            array(
                'multiple' => true,
                'options' => function () {
                    return array(
                        'Option 1' => 'option1',
                        'Option 2' => 'option2',
                    );
                },
            )
        );

        $this->assertEquals(
            array(
                'multiple' => true,
                'choices' => array(
                    'Option 1' => 'option1',
                    'Option 2' => 'option2',
                ),
                'choices_as_values' => true,
            ),
            $this->parameterHandler->convertOptions($parameter)
        );
    }
}
