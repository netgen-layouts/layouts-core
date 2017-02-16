<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form;

use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType as TargetTypeForm;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use stdClass;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TargetTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    protected $targetType;

    public function setUp()
    {
        parent::setUp();

        $this->targetType = new TargetType('type', 42);
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new TargetTypeForm();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::__construct
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage "type" target type form mapper must implement FormMapperInterface interface.
     */
    public function testConstructorThrowsRuntimeException()
    {
        $this->formType = new TargetTypeForm(array('type' => new stdClass()));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::buildForm
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Form mapper for "type" target type does not exist.
     */
    public function testBuildFormThrowsRuntimeException()
    {
        $this->factory->create(
            TargetTypeForm::class,
            new TargetCreateStruct(),
            array('targetType' => $this->targetType)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'targetType' => $this->targetType,
                'data' => new TargetCreateStruct(),
            )
        );

        $this->assertEquals($this->targetType, $options['targetType']);
        $this->assertEquals(new TargetCreateStruct(), $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingTargetType()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidTargetType()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'targetType' => '',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidData()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'targetType' => $this->targetType,
                'data' => '',
            )
        );
    }
}
