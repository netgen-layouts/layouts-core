<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Select;
use Netgen\BlockManager\Parameters\Parameter\Select as SelectParameter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Forms;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Form\FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Select
     */
    protected $handler;

    public function setUp()
    {
        $this->formBuilder = Forms::createFormFactoryBuilder()
            ->addTypeExtension(
                new FormTypeValidatorExtension(
                    $this->createMock(ValidatorInterface::class)
                )
            )
            ->getFormFactory()
            ->createBuilder();

        $this->handler = new Select();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Select::getFormType
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Select::convertOptions
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler::mapForm
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler::getDefaultOptions
     */
    public function testMapForm()
    {
        $this->handler->mapForm(
            $this->formBuilder,
            new SelectParameter(array('options' => array('Heading 1' => 'h1')), true),
            'param_name',
            array(
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
                'validation_groups' => null,
            )
        );

        self::assertCount(1, $this->formBuilder->all());

        $form = $this->formBuilder->get('param_name');

        self::assertEquals('parameters[param_name]', $form->getPropertyPath());
        self::assertEquals('label.param_name', $form->getOption('label'));
        self::assertNotEmpty($form->getOption('constraints'));
        self::assertEquals(array('Heading 1' => 'h1'), $form->getOption('choices'));
        self::assertTrue($form->getRequired());
        self::assertInstanceOf(ChoiceType::class, $form->getType()->getInnerType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Select::getFormType
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Select::convertOptions
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler::mapForm
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler::getDefaultOptions
     */
    public function testMapFormWithOptionsClosure()
    {
        $this->handler->mapForm(
            $this->formBuilder,
            new SelectParameter(array('options' => function () {return array('Heading 1' => 'h1');}), true),
            'param_name',
            array(
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
                'validation_groups' => null,
            )
        );

        self::assertCount(1, $this->formBuilder->all());

        $form = $this->formBuilder->get('param_name');

        self::assertEquals('parameters[param_name]', $form->getPropertyPath());
        self::assertEquals('label.param_name', $form->getOption('label'));
        self::assertNotEmpty($form->getOption('constraints'));
        self::assertEquals(array('Heading 1' => 'h1'), $form->getOption('choices'));
        self::assertTrue($form->getRequired());
        self::assertInstanceOf(ChoiceType::class, $form->getType()->getInnerType());
    }
}
