<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form;

use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType as TargetTypeForm;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetTypeMapper;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
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
        return new TargetTypeForm(['other_type' => new TargetTypeMapper()]);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::buildForm
     * @expectedException \Netgen\BlockManager\Exception\Layout\TargetTypeException
     * @expectedExceptionMessage Form mapper for "type" target type does not exist.
     */
    public function testBuildFormThrowsTargetTypeException()
    {
        $this->factory->create(
            TargetTypeForm::class,
            new TargetCreateStruct(),
            ['target_type' => $this->targetType]
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
                'target_type' => $this->targetType,
                'data' => new TargetCreateStruct(),
            ]
        );

        $this->assertEquals($this->targetType, $options['target_type']);
        $this->assertEquals(new TargetCreateStruct(), $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "target_type" is missing.
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
     * @expectedExceptionMessage The option "target_type" with value "" is expected to be of type "Netgen\BlockManager\Layout\Resolver\TargetTypeInterface", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidTargetType()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'target_type' => '',
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
                'target_type' => $this->targetType,
                'data' => '',
            ]
        );
    }
}
