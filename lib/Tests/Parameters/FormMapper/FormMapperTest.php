<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\Parameter\Text;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text as TextHandler;
use Netgen\BlockManager\Parameters\FormMapper\FormMapper;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Forms;

class FormMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Form\FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\FormMapper
     */
    protected $formMapper;

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
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::__construct
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::mapParameter
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::getPropertyPath
     */
    public function testMapParameter()
    {
        $this->formMapper = new FormMapper(array('text' => new TextHandler()));

        $this->formMapper->mapParameter(
            $this->formBuilder,
            new Text(),
            'param_name',
            array(
                'label_prefix' => 'label_prefix',
                'property_path_prefix' => 'parameters',
            )
        );

        self::assertCount(1, $this->formBuilder->all());

        $form = $this->formBuilder->get('param_name');

        self::assertEquals('parameters[param_name]', $form->getPropertyPath());
        self::assertEquals('label_prefix.param_name', $form->getOption('label'));
        self::assertEquals('text', $form->getType()->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::mapParameter
     * @expectedException \RuntimeException
     */
    public function testMapParameterThrowsRuntimeException()
    {
        $this->formMapper = new FormMapper();
        $this->formMapper->mapParameter(
            $this->formBuilder,
            new Text(),
            'param_name',
            array(
                'label_prefix' => 'label_prefix',
                'property_path_prefix' => 'parameters',
            )
        );
    }
}
