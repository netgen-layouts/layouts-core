<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Form;

use DateTimeImmutable;
use Netgen\BlockManager\Form\DateTimeType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateTimeTypeTest extends FormTestCase
{
    public function getMainType(): FormTypeInterface
    {
        return new DateTimeType();
    }

    /**
     * @covers \Netgen\BlockManager\Form\DateTimeType::buildForm
     * @covers \Netgen\BlockManager\Form\DateTimeType::getTimeZoneList
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'datetime' => '2018-03-31T01:00',
            'timezone' => 'Antarctica/Casey',
        ];

        $form = $this->factory->create(DateTimeType::class);

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());

        $processedData = $form->getData();

        $this->assertInstanceOf(DateTimeImmutable::class, $processedData);
        $this->assertSame('2018-03-31 01:00:00', $processedData->format('Y-m-d H:i:s'));
        $this->assertSame('Antarctica/Casey', $processedData->getTimezone()->getName());

        $this->assertSame($processedData, $form->getData());

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
    public function testSubmitValidDataWithArrayData(): void
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
        $this->assertSame($processedData, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Form\DateTimeTYpe::configureOptions
     */
    public function testConfigureOptions(): void
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
    public function testConfigureOptionsWithDefaultValues(): void
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
    public function testConfigureOptionsWithInvalidUseDateTime(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['use_datetime' => 42]);
    }

    /**
     * @covers \Netgen\BlockManager\Form\DateTimeType::getBlockPrefix
     */
    public function testGetBlockPrefix(): void
    {
        $this->assertSame('ngbm_datetime', $this->formType->getBlockPrefix());
    }
}
