<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form;

use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType as TargetTypeForm;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use stdClass;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TargetTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    private $targetType;

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
     * @expectedException \Netgen\BlockManager\Exception\InvalidInterfaceException
     * @expectedExceptionMessage Form mapper for target type "type" needs to implement "Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface" interface.
     */
    public function testConstructorThrowsInvalidInterfaceException()
    {
        $this->formType = new TargetTypeForm(['type' => new stdClass()]);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::buildForm
     * @expectedException \Netgen\BlockManager\Exception\Layout\TargetTypeException
     * @expectedExceptionMessage Form mapper for "type" target type does not exist.
     */
    public function testBuildFormThrowsTargetTypeException()
    {
        $this->factory->create(
            TargetTypeForm::class,
            new TargetCreateStruct(),
            ['targetType' => $this->targetType]
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
            [
                'targetType' => $this->targetType,
                'data' => new TargetCreateStruct(),
            ]
        );

        $this->assertEquals($this->targetType, $options['targetType']);
        $this->assertEquals(new TargetCreateStruct(), $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "targetType" is missing.
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
     * @expectedExceptionMessage The option "targetType" with value "" is expected to be of type "Netgen\BlockManager\Layout\Resolver\TargetTypeInterface", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidTargetType()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'targetType' => '',
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "data" with value "" is expected to be of type "Netgen\BlockManager\API\Values\LayoutResolver\TargetStruct", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidData()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'targetType' => $this->targetType,
                'data' => '',
            ]
        );
    }
}
