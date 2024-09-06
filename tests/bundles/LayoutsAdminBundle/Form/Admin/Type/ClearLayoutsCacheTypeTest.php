<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Form\Admin\Type;

use Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\ClearLayoutsCacheType;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutList;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ClearLayoutsCacheTypeTest extends FormTestCase
{
    private LayoutList $layouts;

    protected function setUp(): void
    {
        parent::setUp();

        $uuid1 = Uuid::fromString('f06f245a-f951-52c8-bfa3-84c80154eadc');
        $uuid2 = Uuid::fromString('4adf0f00-f6c2-5297-9f96-039bfabe8d3b');

        $this->layouts = new LayoutList(
            [
                Layout::fromArray(['id' => $uuid1, 'name' => 'Layout 1']),
                Layout::fromArray(['id' => $uuid2, 'name' => 'Layout 2']),
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\ClearLayoutsCacheType::buildForm
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\ClearLayoutsCacheType::finishView
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'layouts' => ['f06f245a-f951-52c8-bfa3-84c80154eadc'],
        ];

        $form = $this->factory->create(
            ClearLayoutsCacheType::class,
            null,
            ['layouts' => $this->layouts],
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertIsArray($form->getData());
        self::assertArrayHasKey('layouts', $form->getData());

        self::assertInstanceOf(LayoutList::class, $form->getData()['layouts']);
        self::assertCount(1, $form->getData()['layouts']);
        self::assertSame($this->layouts[0], $form->getData()['layouts'][0]);

        $view = $form->createView();

        $childViews = $view->children['layouts']->children;

        self::assertCount(2, $childViews);

        foreach ($this->layouts as $layout) {
            self::assertArrayHasKey($layout->getId()->toString(), $childViews);

            self::assertArrayHasKey('layout', $childViews[$layout->getId()->toString()]->vars);
            self::assertSame($layout, $childViews[$layout->getId()->toString()]->vars['layout']);
        }
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\ClearLayoutsCacheType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'layouts' => $this->layouts,
            ],
        );

        self::assertSame($this->layouts, $options['layouts']);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type\ClearLayoutsCacheType::configureOptions
     */
    public function testConfigureOptionsWithInvalidLayouts(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "layouts" with value array is expected to be of type "Netgen\Layouts\API\Values\Layout\LayoutList", but is of type "array".');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'layouts' => [],
            ],
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return new ClearLayoutsCacheType();
    }
}
