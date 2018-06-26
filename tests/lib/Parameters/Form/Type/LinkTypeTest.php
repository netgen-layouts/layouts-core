<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form\Type;

use Netgen\BlockManager\Item\CmsItemLoaderInterface;
use Netgen\BlockManager\Item\Registry\ValueTypeRegistry;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper;
use Netgen\BlockManager\Parameters\Form\Type\LinkType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\BlockManager\Parameters\ParameterType\LinkType as LinkParameterType;
use Netgen\BlockManager\Parameters\Value\LinkValue;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LinkTypeTest extends FormTestCase
{
    use ExportObjectTrait;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterType\LinkType
     */
    private $parameterType;

    public function setUp(): void
    {
        $this->parameterType = new LinkParameterType(
            new ValueTypeRegistry([]),
            new RemoteIdConverter($this->createMock(CmsItemLoaderInterface::class))
        );

        parent::setUp();
    }

    public function getMainType(): FormTypeInterface
    {
        return new LinkType();
    }

    public function getTypes(): array
    {
        $backendRegistry = new BackendRegistry(['value' => $this->createMock(BackendInterface::class)]);

        return [
            new ContentBrowserDynamicType(
                $backendRegistry,
                ['value']
            ),
        ];
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::buildForm
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'link_type' => 'url',
            'link_suffix' => '?suffix',
            'new_window' => true,
            'url' => 'http://www.google.com',
        ];

        $parameterDefinition = new ParameterDefinition(
            [
                'type' => $this->parameterType,
            ]
        );

        $formBuilder = $this->factory->createBuilder(LinkType::class);
        $formBuilder->setDataMapper(new LinkDataMapper($parameterDefinition));
        $form = $formBuilder->getForm();

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());

        $formData = $form->getData();

        $this->assertInstanceOf(LinkValue::class, $formData);

        $this->assertSame(
            [
                'linkType' => 'url',
                'link' => 'http://www.google.com',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            ],
            $this->exportObject($formData)
        );

        // View test

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::buildForm
     */
    public function testSubmitInvalidData(): void
    {
        $submittedData = [
            'link_type' => 'unknown',
            'link_suffix' => '?suffix',
            'new_window' => true,
            'url' => 'http://www.google.com',
        ];

        $parameterDefinition = new ParameterDefinition(
            [
                'type' => $this->parameterType,
            ]
        );

        $formBuilder = $this->factory->createBuilder(LinkType::class);
        $formBuilder->setDataMapper(new LinkDataMapper($parameterDefinition));
        $form = $formBuilder->getForm();

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());

        $formData = $form->getData();

        $this->assertInstanceOf(LinkValue::class, $formData);

        $this->assertSame(
            [
                'linkType' => null,
                'link' => null,
                'linkSuffix' => null,
                'newWindow' => false,
            ],
            $this->exportObject($formData)
        );

        // View test

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::buildView
     */
    public function testBuildView(): void
    {
        $parameterDefinition = new ParameterDefinition(
            [
                'type' => $this->parameterType,
            ]
        );

        $formBuilder = $this->factory->createBuilder(LinkType::class);
        $formBuilder->setDataMapper(new LinkDataMapper($parameterDefinition));
        $form = $formBuilder->getForm();

        $form->submit(
            [
                'link_type' => 'url',
                'link_suffix' => '?suffix',
                'new_window' => true,
                'url' => 'http://www.google.com',
            ]
        );

        $form->get('link')->addError(new FormError('an error'));
        $form->createView();

        $errors = $form->get('url')->getErrors();

        $this->assertCount(1, $errors);
        $this->assertSame('an error', iterator_to_array($errors)[0]->getMessage());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::buildView
     */
    public function testBuildViewWithInvalidData(): void
    {
        $parameterDefinition = new ParameterDefinition(
            [
                'type' => $this->parameterType,
            ]
        );

        $formBuilder = $this->factory->createBuilder(LinkType::class);
        $formBuilder->setDataMapper(new LinkDataMapper($parameterDefinition));
        $form = $formBuilder->getForm();

        $form->submit(
            [
                'link_type' => 'unknown',
                'link_suffix' => '?suffix',
                'new_window' => true,
                'url' => 'http://www.google.com',
            ]
        );

        $form->get('link')->addError(new FormError('an error'));
        $form->createView();

        $this->assertCount(0, $form->get('url')->getErrors());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = [
            'value_types' => ['value'],
        ];

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->assertSame(
            [
                'translation_domain' => 'ngbm_forms',
                'value_types' => ['value'],
            ],
            $resolvedOptions
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::configureOptions
     */
    public function testConfigureOptionsWithEmptyValueTypes(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $resolvedOptions = $optionsResolver->resolve();

        $this->assertSame(
            [
                'translation_domain' => 'ngbm_forms',
                'value_types' => [],
            ],
            $resolvedOptions
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "value_types" with value 42 is expected to be of type "array", but is of type "integer".
     */
    public function testConfigureOptionsWithInvalidParameters(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'value_types' => 42,
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Type\LinkType::getBlockPrefix
     */
    public function testGetBlockPrefix(): void
    {
        $this->assertSame('ngbm_link', $this->formType->getBlockPrefix());
    }
}
