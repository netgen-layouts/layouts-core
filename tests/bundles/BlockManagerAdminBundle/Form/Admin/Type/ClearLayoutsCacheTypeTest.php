<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\Form\Admin\Type;

use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearLayoutsCacheType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ClearLayoutsCacheTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout[]
     */
    private $layouts;

    public function setUp(): void
    {
        parent::setUp();

        $this->layouts = [
            42 => new Layout(['id' => 42, 'name' => 'Layout 42']),
            24 => new Layout(['id' => 24, 'name' => 'Layout 24']),
        ];
    }

    public function getMainType(): FormTypeInterface
    {
        return new ClearLayoutsCacheType();
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

        $this->assertTrue($form->isSynchronized());
        $this->assertSame(['layouts' => [$this->layouts[42]]], $form->getData());

        $view = $form->createView();

        $childViews = $view->children['layouts']->children;

        $this->assertCount(2, $childViews);

        foreach ($this->layouts as $id => $layout) {
            $this->assertArrayHasKey($id, $childViews);

            $this->assertArrayHasKey('layout', $childViews[$id]->vars);
            $this->assertSame($layout, $childViews[$id]->vars['layout']);
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

        $this->assertSame($this->layouts, $options['layouts']);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearLayoutsCacheType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "layouts" with value array is invalid.
     */
    public function testConfigureOptionsWithInvalidLayout(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'layouts' => [42],
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type\ClearLayoutsCacheType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "layouts" with value 42 is expected to be of type "array", but is of type "integer".
     */
    public function testConfigureOptionsWithInvalidLayouts(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'layouts' => 42,
            ]
        );
    }
}
