<?php

namespace Netgen\BlockManager\Tests\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\Parameter\Text;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Text as TextHandler;
use Netgen\BlockManager\Parameters\FormMapper\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Forms;
use DateTime;
use PHPUnit\Framework\TestCase;

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
     * @expectedException \RuntimeException
     */
    public function testConstructorThrowsRuntimeExceptionWithNoParameterHandlerInterface()
    {
        $formMapper = new FormMapper(
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
        self::assertInstanceOf(TextType::class, $form->getType()->getInnerType());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::mapParameter
     * @covers \Netgen\BlockManager\Parameters\FormMapper\FormMapper::configureOptions
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
