<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\Form\Admin\Type;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutList;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearLayoutsCacheType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ClearLayoutsCacheTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Layout\LayoutList
     */
    private $layouts;

    public function setUp(): void
    {
        parent::setUp();

        $this->layouts = new LayoutList(
            [
                Layout::fromArray(['id' => 42, 'name' => 'Layout 42']),
                Layout::fromArray(['id' => 24, 'name' => 'Layout 24']),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearLayoutsCacheType::buildForm
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearLayoutsCacheType::finishView
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'layouts' => [42],
        ];

        $form = $this->factory->create(
            ClearLayoutsCacheType::class,
            null,
            ['layouts' => $this->layouts]
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        self::assertInternalType('array', $form->getData());
        self::assertArrayHasKey('layouts', $form->getData());

        self::assertInstanceOf(LayoutList::class, $form->getData()['layouts']);
        self::assertCount(1, $form->getData()['layouts']);
        self::assertSame($this->layouts[0], $form->getData()['layouts'][0]);

        $view = $form->createView();

        $childViews = $view->children['layouts']->children;

        self::assertCount(2, $childViews);

        foreach ($this->layouts as $layout) {
            self::assertArrayHasKey($layout->getId(), $childViews);

            self::assertArrayHasKey('layout', $childViews[$layout->getId()]->vars);
            self::assertSame($layout, $childViews[$layout->getId()]->vars['layout']);
        }
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearLayoutsCacheType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'layouts' => $this->layouts,
            ]
        );

        self::assertSame($this->layouts, $options['layouts']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearLayoutsCacheType::configureOptions
     */
    public function testConfigureOptionsWithInvalidLayouts(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "layouts" with value 42 is expected to be of type "Netgen\\BlockManager\\API\\Values\\Layout\\LayoutList", but is of type "integer".');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'layouts' => 42,
            ]
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        return new ClearLayoutsCacheType();
    }
}
