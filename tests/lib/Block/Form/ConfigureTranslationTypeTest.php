<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\Form;

use Netgen\BlockManager\Block\Form\ConfigureTranslationType;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConfigureTranslationTypeTest extends FormTestCase
{
    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new ConfigureTranslationType();
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\ConfigureTranslationType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\ConfigureTranslationType::buildView
     */
    public function testSubmitValidData()
    {
        $block = new Block(['isTranslatable' => false]);

        $submittedData = ['translatable' => true];

        $form = $this->factory->create(
            ConfigureTranslationType::class,
            null,
            [
                'block' => $block,
            ]
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($submittedData, $form->getData());

        $view = $form->createView();

        $this->assertArrayHasKey('block', $view->vars);
        $this->assertEquals($block, $view->vars['block']);

        $this->assertArrayHasKey('translatable', $view->children);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\ConfigureTranslationType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $block = new Block();
        $options = ['block' => $block];
        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertEquals('ngbm_forms', $resolvedOptions['translation_domain']);
        $this->assertEquals($block, $resolvedOptions['block']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\ConfigureTranslationType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "block" with value 42 is expected to be of type "Netgen\BlockManager\API\Values\Block\Block", but is of type "integer".
     */
    public function testConfigureOptionsWithInvalidBlock()
    {
        $optionsResolver = new OptionsResolver();
        $this->formType->configureOptions($optionsResolver);
        $optionsResolver->resolve(['block' => 42]);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\ConfigureTranslationType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "block" is missing.
     */
    public function testConfigureOptionsWithMissingBlock()
    {
        $optionsResolver = new OptionsResolver();
        $this->formType->configureOptions($optionsResolver);
        $optionsResolver->resolve();
    }
}
