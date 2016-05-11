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

        $this->formMapper = new FormMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::addParameterHandler
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::mapParameter
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::getPropertyPath
     */
    public function testMapParameter()
    {
        $this->formMapper->addParameterHandler('text', new TextHandler());

        $this->formMapper->mapParameter(
            $this->formBuilder,
            new Text(),
            'param_name',
            'label_prefix',
            null
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
        $this->formMapper->mapParameter($this->formBuilder, new Text(), 'param_name', 'label_prefix');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::addParameterHandler
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::mapHiddenParameter
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::getPropertyPath
     */
    public function testMapHiddenParameter()
    {
        $this->formMapper->addParameterHandler('text', new TextHandler());

        $this->formMapper->mapHiddenParameter(
            $this->formBuilder,
            new Text(),
            'param_name',
            null,
            null
        );

        self::assertCount(1, $this->formBuilder->all());

        self::assertEquals('hidden', $this->formBuilder->get('param_name')->getType()->getName());
        self::assertEquals('param_name', $this->formBuilder->get('param_name')->getPropertyPath());
    }
}
