<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\ParameterDefinition\TextLine;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\TextLine as TextLineHandler;
use Netgen\BlockManager\Parameters\FormMapper\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Forms;
use PHPUnit\Framework\TestCase;
use DateTime;

class FormMapperTest extends TestCase
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
                    $this->createMock(ValidatorInterface::class)
                )
            )
            ->getFormFactory()
            ->createBuilder();
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::__construct
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testConstructorThrowsRuntimeExceptionWithNoParameterHandlerInterface()
    {
        new FormMapper(array($this->createMock(DateTime::class)));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::__construct
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::mapParameter
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::configureOptions
     */
    public function testMapParameter()
    {
        $this->formMapper = new FormMapper(array('text_line' => new TextLineHandler()));

        $this->formMapper->mapParameter(
            $this->formBuilder,
            new TextLine(),
            'param_name',
            array(
                'label_prefix' => 'label_prefix',
                'property_path_prefix' => 'parameters',
            )
        );

        $this->assertCount(1, $this->formBuilder->all());

        $form = $this->formBuilder->get('param_name');

        $this->assertEquals('parameters[param_name]', $form->getPropertyPath());
        $this->assertEquals('label_prefix.param_name', $form->getOption('label'));
        $this->assertInstanceOf(TextType::class, $form->getType()->getInnerType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::mapParameter
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::configureOptions
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     */
    public function testMapParameterThrowsRuntimeException()
    {
        $this->formMapper = new FormMapper();

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
