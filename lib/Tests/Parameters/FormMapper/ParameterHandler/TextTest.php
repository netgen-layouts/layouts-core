<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text;
use Netgen\BlockManager\Parameters\Parameter\Text as TextParameter;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Forms;

class TextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Form\FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text
     */
    protected $handler;

    public function setUp()
    {
        $this->formBuilder = Forms::createFormFactoryBuilder()
            ->addTypeExtension(
                new FormTypeValidatorExtension(
                    $this->getMock(ValidatorInterface::class)
                )
            )
            ->getFormFactory()
            ->createBuilder('form');

        $this->handler = new Text();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text::getFormType
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler::mapForm
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler::getDefaultOptions
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler::convertOptions
     * @covers \Netgen\BlockManager\Parameters\FormMapper\ParameterHandler::getPropertyPath
     */
    public function testMapForm()
    {
        $this->handler->mapForm(
            $this->formBuilder,
            new TextParameter(array(), true),
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
        self::assertTrue($form->getRequired());
        self::assertEquals('text', $form->getType()->getName());
    }
}
