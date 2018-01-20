<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType as ConditionTypeForm;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use stdClass;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConditionTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    private $conditionType;

    public function setUp()
    {
        parent::setUp();

        $this->conditionType = new ConditionType('type');
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new ConditionTypeForm();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::__construct
     * @expectedException \Netgen\BlockManager\Exception\InvalidInterfaceException
     * @expectedExceptionMessage Form mapper for condition type "type" needs to implement "Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface" interface.
     */
    public function testConstructorThrowsInvalidInterfaceException()
    {
        $this->formType = new ConditionTypeForm(array('type' => new stdClass()));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::buildForm
     * @expectedException \Netgen\BlockManager\Exception\Layout\ConditionTypeException
     * @expectedExceptionMessage Form mapper for "type" condition type does not exist.
     */
    public function testBuildFormThrowsConditionTypeException()
    {
        $this->factory->create(
            ConditionTypeForm::class,
            new ConditionCreateStruct(),
            array('conditionType' => $this->conditionType)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'conditionType' => $this->conditionType,
                'data' => new ConditionCreateStruct(),
            )
        );

        $this->assertEquals($this->conditionType, $options['conditionType']);
        $this->assertEquals(new ConditionCreateStruct(), $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingConditionType()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidConditionType()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'conditionType' => '',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidData()
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            array(
                'conditionType' => $this->conditionType,
                'data' => '',
            )
        );
    }
}
