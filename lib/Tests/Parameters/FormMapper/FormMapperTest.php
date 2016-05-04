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
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::addParameterHandler
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::mapParameter
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::getPropertyPath
     */
    public function testMapParameter()
    {
        $formBuilder = Forms::createFormFactoryBuilder()
            ->addTypeExtension(
                new FormTypeValidatorExtension(
                    $this->getMock(ValidatorInterface::class)
                )
            )
            ->getFormFactory()
            ->createBuilder('form');

        $parameterMapper = new FormMapper();
        $parameterMapper->addParameterHandler('text', new TextHandler());

        $parameterMapper->mapParameter(
            $formBuilder,
            new Text(),
            'param_name',
            null
        );

        self::assertCount(1, $formBuilder->all());

        self::assertEquals('text', $formBuilder->get('param_name')->getType()->getName());
        self::assertEquals('parameters[param_name]', $formBuilder->get('param_name')->getPropertyPath());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::mapParameter
     * @expectedException \RuntimeException
     */
    public function testMapParameterThrowsRuntimeException()
    {
        $formBuilder = Forms::createFormFactoryBuilder()
            ->addTypeExtension(
                new FormTypeValidatorExtension(
                    $this->getMock(ValidatorInterface::class)
                )
            )
            ->getFormFactory()
            ->createBuilder('form');

        $parameterMapper = new FormMapper();

        $parameterMapper->mapParameter($formBuilder, new Text(), 'param_name');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::addParameterHandler
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::mapHiddenParameter
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::getPropertyPath
     */
    public function testMapHiddenParameter()
    {
        $formBuilder = Forms::createFormFactoryBuilder()
            ->addTypeExtension(
                new FormTypeValidatorExtension(
                    $this->getMock(ValidatorInterface::class)
                )
            )
            ->getFormFactory()
            ->createBuilder('form');

        $parameterMapper = new FormMapper();
        $parameterMapper->addParameterHandler('text', new TextHandler());

        $parameterMapper->mapHiddenParameter(
            $formBuilder,
            new Text(),
            'param_name',
            null,
            null
        );

        self::assertCount(1, $formBuilder->all());

        self::assertEquals('hidden', $formBuilder->get('param_name')->getType()->getName());
        self::assertEquals('param_name', $formBuilder->get('param_name')->getPropertyPath());
    }
}
