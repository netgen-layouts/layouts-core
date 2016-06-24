<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\FormMapper\DataTransformer\ParameterFilterDataTransformer;
use Netgen\BlockManager\Parameters\Parameter\TextLine;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextLine as TextLineHandler;
use Netgen\BlockManager\Parameters\FormMapper\FormMapper;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistry;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Forms;
use PHPUnit\Framework\TestCase;
use DateTime;

class FormMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface
     */
    protected $parameterFilterRegistry;

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
        $this->parameterFilterRegistry = new ParameterFilterRegistry();

        $this->formBuilder = Forms::createFormFactoryBuilder()
            ->addTypeExtension(
                new FormTypeValidatorExtension(
                    $this->createMock(ValidatorInterface::class)
                )
            )
            ->getFormFactory()
            ->createBuilder();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::__construct
     * @expectedException \RuntimeException
     */
    public function testConstructorThrowsRuntimeExceptionWithNoParameterHandlerInterface()
    {
        $formMapper = new FormMapper(
            $this->parameterFilterRegistry,
            array($this->createMock(DateTime::class))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::__construct
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::mapParameter
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::configureOptions
     */
    public function testMapParameter()
    {
        $this->formMapper = new FormMapper(
            $this->parameterFilterRegistry,
            array('text_line' => new TextLineHandler())
        );

        $this->formMapper->mapParameter(
            $this->formBuilder,
            new TextLine(),
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
        self::assertInstanceOf(TextType::class, $form->getType()->getInnerType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::__construct
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::mapParameter
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::configureOptions
     */
    public function testMapParameterWithFilterTransformer()
    {
        $this->parameterFilterRegistry->addParameterFilter(
            'text_line',
            new ParameterFilter()
        );

        $this->formMapper = new FormMapper(
            $this->parameterFilterRegistry,
            array('text_line' => new TextLineHandler())
        );

        $this->formMapper->mapParameter(
            $this->formBuilder,
            new TextLine(),
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
        self::assertInstanceOf(TextType::class, $form->getType()->getInnerType());

        self::assertCount(1, $form->getModelTransformers());
        self::assertInstanceOf(ParameterFilterDataTransformer::class, $form->getModelTransformers()[0]);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::mapParameter
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::configureOptions
     * @expectedException \RuntimeException
     */
    public function testMapParameterThrowsRuntimeException()
    {
        $this->formMapper = new FormMapper(
            $this->parameterFilterRegistry
        );

        $this->formMapper->mapParameter(
            $this->formBuilder,
            new TextLine(),
            'param_name',
            array(
                'label_prefix' => 'label_prefix',
                'property_path_prefix' => 'parameters',
            )
        );
    }
}
