<?php

namespace Netgen\BlockManager\Tests\Layout\Form;

use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Layout\Form\AddLocaleType;
use Netgen\BlockManager\Locale\LocaleProviderInterface;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddLocaleTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Locale\LocaleProviderInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $localeProviderMock;

    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout
     */
    private $layout;

    public function setUp()
    {
        $this->localeProviderMock = $this->createMock(LocaleProviderInterface::class);

        $this->localeProviderMock
            ->expects($this->any())
            ->method('getAvailableLocales')
            ->will(
                $this->returnValue(
                    ['hr_HR' => 'Croatian', 'fr_FR' => 'French', 'en_GB' => 'English', 'de_DE' => 'German']
                )
            );

        $this->layout = new Layout(['availableLocales' => ['en_GB', 'de_DE'], 'mainLocale' => 'en_GB']);

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new AddLocaleType($this->localeProviderMock);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\AddLocaleType::__construct
     * @covers \Netgen\BlockManager\Layout\Form\AddLocaleType::buildForm
     */
    public function testSubmitValidData()
    {
        $submittedData = [
            'locale' => 'hr_HR',
            'sourceLocale' => 'en_GB',
        ];

        $form = $this->factory->create(
            AddLocaleType::class,
            null,
            [
                'layout' => $this->layout,
            ]
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($submittedData, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\AddLocaleType::configureOptions
     */
    public function testConfigureOptions()
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $layout = new Layout();
        $options = ['layout' => $layout];
        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertEquals('ngbm_forms', $resolvedOptions['translation_domain']);
        $this->assertEquals($layout, $resolvedOptions['layout']);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\AddLocaleType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "layout" with value 42 is expected to be of type "Netgen\BlockManager\API\Values\Layout\Layout", but is of type "integer".
     */
    public function testConfigureOptionsWithInvalidLayout()
    {
        $optionsResolver = new OptionsResolver();
        $this->formType->configureOptions($optionsResolver);
        $optionsResolver->resolve(['layout' => 42]);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Form\AddLocaleType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "layout" is missing.
     */
    public function testConfigureOptionsWithMissingLayout()
    {
        $optionsResolver = new OptionsResolver();
        $this->formType->configureOptions($optionsResolver);
        $optionsResolver->resolve();
    }
}
