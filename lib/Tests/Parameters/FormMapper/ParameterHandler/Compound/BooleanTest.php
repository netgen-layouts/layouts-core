<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler\Compound;

use Netgen\BlockManager\Parameters\FormMapper\FormMapper;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Compound\Boolean;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text;
use Netgen\BlockManager\Parameters\FormMapper\Type\CompoundBooleanType;
use Netgen\BlockManager\Parameters\Parameter\Compound\Boolean as BooleanParameter;
use Netgen\BlockManager\Parameters\Parameter\Text as TextParameter;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Forms;

class BooleanTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Form\FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Boolean
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new Boolean();

        $this->formBuilder = Forms::createFormFactoryBuilder()
            ->addType(
                new CompoundBooleanType(
                    new FormMapper(
                        array(
                            'compound_boolean' => $this->handler,
                            'text' => new Text(),
                        )
                    )
                )
            )
            ->addTypeExtension(
                new FormTypeValidatorExtension(
                    $this->getMock(ValidatorInterface::class)
                )
            )
            ->getFormFactory()
            ->createBuilder('form');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Boolean::getFormType
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler::mapForm
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler::getDefaultOptions
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler::convertOptions
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler::getPropertyPath
     */
    public function testMapForm()
    {
        $this->handler->mapForm(
            $this->formBuilder,
            new BooleanParameter(array('sub_param' => new TextParameter(array(), true)), array(), true),
            'param_name',
            array(
                'label_prefix' => 'label',
                'property_path_prefix' => 'parameters',
                'validation_groups' => null,
            )
        );

        self::assertCount(1, $this->formBuilder->all());

        $mainForm = $this->formBuilder->get('param_name');

        self::assertCount(2, $mainForm->all());

        $form = $mainForm->get('_self');

        self::assertEquals('parameters[param_name]', $form->getPropertyPath());
        self::assertEquals('label.param_name', $form->getOption('label'));
        self::assertNotEmpty($form->getOption('constraints'));
        self::assertTrue($form->getRequired());
        self::assertEquals('checkbox', $form->getType()->getName());

        $form = $mainForm->get('sub_param');

        self::assertEquals('parameters[sub_param]', $form->getPropertyPath());
        self::assertEquals('label.sub_param', $form->getOption('label'));
        self::assertNotEmpty($form->getOption('constraints'));
        self::assertTrue($form->getRequired());
        self::assertEquals('text', $form->getType()->getName());
    }
}
