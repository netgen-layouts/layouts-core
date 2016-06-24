<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\Parameter\Choice as ChoiceParameter;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Choice;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;

class ChoiceTest extends TestCase
{
    /**
     * @var array
     */
    protected $choicesAsValues;

    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Choice
     */
    protected $parameterHandler;

    public function setUp()
    {
        // choices_as_values is deprecated on Symfony >= 3.1,
        // while on previous versions needs to be set to true
        $this->choicesAsValues = Kernel::VERSION_ID < 30100 ?
            array('choices_as_values' => true) :
            array();

        $this->parameterHandler = new Choice();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Choice::__construct
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Choice::getFormType
     */
    public function testGetFormType()
    {
        self::assertEquals(ChoiceType::class, $this->parameterHandler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Choice::convertOptions
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

        self::assertEquals(
            array(
                'multiple' => true,
                'choices' => array(
                    'Option 1' => 'option1',
                    'Option 2' => 'option2',
                ),
            ) + $this->choicesAsValues,
            $this->parameterHandler->convertOptions($parameter)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Choice::convertOptions
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

        self::assertEquals(
            array(
                'multiple' => true,
                'choices' => array(
                    'Option 1' => 'option1',
                    'Option 2' => 'option2',
                ),
            ) + $this->choicesAsValues,
            $this->parameterHandler->convertOptions($parameter)
        );
    }
}
