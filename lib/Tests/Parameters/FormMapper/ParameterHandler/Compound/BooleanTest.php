<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler\Compound;

use Netgen\BlockManager\Parameters\FormMapper\FormMapper;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Compound\Boolean;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text;
use Netgen\BlockManager\Parameters\FormMapper\Type\CompoundBooleanType;
use Netgen\BlockManager\Parameters\Parameter\Compound\Boolean as BooleanParameter;
use Netgen\BlockManager\Parameters\Parameter\Text as TextParameter;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Forms;
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    /**
     * @var \Symfony\Component\Form\FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Compound\Boolean
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
                    $this->createMock(ValidatorInterface::class)
                )
            )
            ->getFormFactory()
            ->createBuilder();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Compound\Boolean::getFormType
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Compound\Boolean::getDefaultOptions
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Compound\Boolean::convertOptions
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler::mapForm
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
        self::assertInstanceOf(CheckboxType::class, $form->getType()->getInnerType());

        $form = $mainForm->get('sub_param');

        self::assertEquals('parameters[sub_param]', $form->getPropertyPath());
        self::assertEquals('label.sub_param', $form->getOption('label'));
        self::assertNotEmpty($form->getOption('constraints'));
        self::assertTrue($form->getRequired());
        self::assertInstanceOf(TextType::class, $form->getType()->getInnerType());
    }
}
