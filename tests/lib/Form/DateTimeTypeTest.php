<?php

namespace Netgen\BlockManager\Tests\Form;

use DateTimeImmutable;
use DateTimeZone;
use Netgen\BlockManager\Form\DateTimeType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Netgen\BlockManager\Utils\DateTimeUtils;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateTimeTypeTest extends FormTestCase
{
    public static function setUpBeforeClass()
    {
        ClockMock::register(DateTimeUtils::class);
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new DateTimeType();
    }

    /**
     * @covers \Netgen\BlockManager\Form\DateTimeType::buildForm
     * @covers \Netgen\BlockManager\Form\DateTimeType::getTimeZoneList
     */
    public function testSubmitValidData()
    {
        $submittedData = [
            'datetime' => '2018-03-31T01:00',
            'timezone' => 'Antarctica/Casey',
        ];

        $processedData = new DateTimeImmutable(
            '2018-03-31 01:00:00',
            new DateTimeZone('Antarctica/Casey')
        );

        $form = $this->factory->create(DateTimeType::class);

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($processedData, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Form\DateTimeType::buildForm
     * @covers \Netgen\BlockManager\Form\DateTimeType::getTimeZoneList
     */
    public function testSubmitValidDataWithArrayData()
    {
        $submittedData = [
            'datetime' => '2018-03-31T01:00',
            'timezone' => 'Antarctica/Casey',
        ];

        $processedData = [
            'datetime' => '2018-03-31 01:00:00',
            'timezone' => 'Antarctica/Casey',
        ];

        $form = $this->factory->create(DateTimeType::class, null, ['use_datetime' => false]);

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($processedData, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Form\DateTimeTYpe::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = [
            'use_datetime' => false,
        ];

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertFalse($resolvedOptions['use_datetime']);
        $this->assertFalse($resolvedOptions['error_bubbling']);
    }

    /**
     * @covers \Netgen\BlockManager\Form\DateTimeTYpe::configureOptions
     */
    public function testConfigureOptionsWithDefaultValues()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $resolvedOptions = $optionsResolver->resolve([]);

        $this->assertTrue($resolvedOptions['use_datetime']);
        $this->assertFalse($resolvedOptions['error_bubbling']);
    }

    /**
     * @covers \Netgen\BlockManager\Form\DateTimeTYpe::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "use_datetime" with value 42 is expected to be of type "bool", but is of type "integer".
     */
    public function testConfigureOptionsWithInvalidUseDateTime()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['use_datetime' => 42]);
    }

    /**
     * @covers \Netgen\BlockManager\Form\DateTimeType::getBlockPrefix
     */
    public function testGetBlockPrefix()
    {
        $this->assertEquals('ngbm_datetime', $this->formType->getBlockPrefix());
    }
}
