<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Form\ParameterMapper;

use Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandler\Text;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterMapper;
use Netgen\BlockManager\Tests\BlockDefinition\Stubs\BlockDefinition;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Forms;

class ParameterMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterMapper::addParameterHandler
     * @covers \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterMapper::mapParameters
     */
    public function testMapParameters()
    {
        $formBuilder = Forms::createFormFactoryBuilder()
            ->addTypeExtension(
                new FormTypeValidatorExtension(
                    $this->getMock(ValidatorInterface::class)
                )
            )
            ->getFormFactory()
            ->createBuilder('form');

        $parameterMapper = new ParameterMapper();
        $parameterMapper->addParameterHandler('text', new Text());

        $parameterMapper->mapParameters($formBuilder, new BlockDefinition());

        self::assertCount(2, $formBuilder->all());

        self::assertEquals('text', $formBuilder->get('css_id')->getType()->getName());
        self::assertEquals('text', $formBuilder->get('css_class')->getType()->getName());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterMapper::mapParameters
     * @expectedException \RuntimeException
     */
    public function testMapParametersThrowsRuntimeException()
    {
        $formBuilder = Forms::createFormFactoryBuilder()
            ->addTypeExtension(
                new FormTypeValidatorExtension(
                    $this->getMock(ValidatorInterface::class)
                )
            )
            ->getFormFactory()
            ->createBuilder('form');

        $parameterMapper = new ParameterMapper();

        $parameterMapper->mapParameters($formBuilder, new BlockDefinition());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterMapper::addParameterHandler
     * @covers \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterMapper::mapHiddenParameters
     */
    public function testMapHiddenParameters()
    {
        $formBuilder = Forms::createFormFactoryBuilder()
            ->addTypeExtension(
                new FormTypeValidatorExtension(
                    $this->getMock(ValidatorInterface::class)
                )
            )
            ->getFormFactory()
            ->createBuilder('form');

        $parameterMapper = new ParameterMapper();
        $parameterMapper->addParameterHandler('text', new Text());

        $parameterMapper->mapHiddenParameters($formBuilder, new BlockDefinition());

        self::assertCount(2, $formBuilder->all());

        self::assertEquals('hidden', $formBuilder->get('css_id')->getType()->getName());
        self::assertEquals('hidden', $formBuilder->get('css_class')->getType()->getName());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterMapper::mapHiddenParameters
     * @expectedException \RuntimeException
     */
    public function testMapHiddenParametersThrowsRuntimeException()
    {
        $formBuilder = Forms::createFormFactoryBuilder()
            ->addTypeExtension(
                new FormTypeValidatorExtension(
                    $this->getMock(ValidatorInterface::class)
                )
            )
            ->getFormFactory()
            ->createBuilder('form');

        $parameterMapper = new ParameterMapper();

        $parameterMapper->mapParameters($formBuilder, new BlockDefinition());
    }
}
