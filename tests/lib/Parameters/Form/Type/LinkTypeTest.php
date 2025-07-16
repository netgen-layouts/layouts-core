<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form\Type;

use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use Netgen\ContentBrowser\Registry\ConfigRegistry;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\Registry\ValueTypeRegistry;
use Netgen\Layouts\Parameters\Form\Type\DataMapper\LinkDataMapper;
use Netgen\Layouts\Parameters\Form\Type\LinkType;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType\ItemLink\RemoteIdConverter;
use Netgen\Layouts\Parameters\ParameterType\LinkType as LinkParameterType;
use Netgen\Layouts\Parameters\Value\LinkValue;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_keys;

final class LinkTypeTest extends FormTestCase
{
    use ExportObjectTrait;

    private LinkParameterType $parameterType;

    protected function setUp(): void
    {
        $this->parameterType = new LinkParameterType(
            new ValueTypeRegistry([]),
            new RemoteIdConverter($this->createMock(CmsItemLoaderInterface::class)),
        );

        parent::setUp();
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\LinkType::buildForm
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'link_type' => 'url',
            'link_suffix' => '?suffix',
            'new_window' => true,
            'url' => 'https://netgen.io',
        ];

        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => $this->parameterType,
            ],
        );

        $formBuilder = $this->factory->createBuilder(LinkType::class);
        $formBuilder->setDataMapper(new LinkDataMapper($parameterDefinition));
        $form = $formBuilder->getForm();

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        $formData = $form->getData();

        self::assertInstanceOf(LinkValue::class, $formData);

        self::assertSame(
            [
                'link' => 'https://netgen.io',
                'linkSuffix' => '?suffix',
                'linkType' => 'url',
                'newWindow' => true,
            ],
            $this->exportObject($formData),
        );

        // View test

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            self::assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\LinkType::buildForm
     */
    public function testSubmitInvalidData(): void
    {
        $submittedData = [
            'link_type' => 'unknown',
            'link_suffix' => '?suffix',
            'new_window' => true,
            'url' => 'https://netgen.io',
        ];

        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => $this->parameterType,
            ],
        );

        $formBuilder = $this->factory->createBuilder(LinkType::class);
        $formBuilder->setDataMapper(new LinkDataMapper($parameterDefinition));
        $form = $formBuilder->getForm();

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());

        $formData = $form->getData();

        self::assertInstanceOf(LinkValue::class, $formData);

        self::assertSame(
            [
                'link' => '',
                'linkSuffix' => '',
                'linkType' => '',
                'newWindow' => false,
            ],
            $this->exportObject($formData),
        );

        // View test

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            self::assertArrayHasKey($key, $children);
        }
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\LinkType::buildView
     */
    public function testBuildView(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => $this->parameterType,
            ],
        );

        $formBuilder = $this->factory->createBuilder(LinkType::class);
        $formBuilder->setDataMapper(new LinkDataMapper($parameterDefinition));
        $form = $formBuilder->getForm();

        $form->submit(
            [
                'link_type' => 'url',
                'link_suffix' => '?suffix',
                'new_window' => true,
                'url' => 'https://netgen.io',
            ],
        );

        $form->get('link')->addError(new FormError('an error'));
        $form->createView();

        $errors = $form->get('url')->getErrors();
        self::assertCount(1, $errors);

        /** @var \Symfony\Component\Form\FormError $firstError */
        $firstError = [...$errors][0];
        self::assertSame('an error', $firstError->getMessage());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\LinkType::buildView
     */
    public function testBuildViewWithInvalidData(): void
    {
        $parameterDefinition = ParameterDefinition::fromArray(
            [
                'type' => $this->parameterType,
            ],
        );

        $formBuilder = $this->factory->createBuilder(LinkType::class);
        $formBuilder->setDataMapper(new LinkDataMapper($parameterDefinition));
        $form = $formBuilder->getForm();

        $form->submit(
            [
                'link_type' => 'unknown',
                'link_suffix' => '?suffix',
                'new_window' => true,
                'url' => 'https://netgen.io',
            ],
        );

        $form->get('link')->addError(new FormError('an error'));
        $form->createView();

        self::assertCount(0, $form->get('url')->getErrors());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\LinkType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = [
            'value_types' => ['value'],
        ];

        $resolvedOptions = $optionsResolver->resolve($options);

        self::assertSame(
            [
                'translation_domain' => 'nglayouts_forms',
                'value_types' => ['value'],
            ],
            $resolvedOptions,
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\LinkType::configureOptions
     */
    public function testConfigureOptionsWithInvalidValueType(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessageMatches('/^The option "value_types" with value array is expected to be of type "string\[\]", but one of the elements is of type "int(eger)?".$/');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(['value_types' => [42]]);
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\LinkType::configureOptions
     */
    public function testConfigureOptionsWithEmptyValueTypes(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $resolvedOptions = $optionsResolver->resolve();

        self::assertSame(
            [
                'translation_domain' => 'nglayouts_forms',
                'value_types' => [],
            ],
            $resolvedOptions,
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\LinkType::configureOptions
     */
    public function testConfigureOptionsWithInvalidParameters(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessageMatches('/^The option "value_types" with value 42 is expected to be of type "string\[\]", but is of type "int(eger)?".$/');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'value_types' => 42,
            ],
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Form\Type\LinkType::getBlockPrefix
     */
    public function testGetBlockPrefix(): void
    {
        self::assertSame('nglayouts_link', $this->formType->getBlockPrefix());
    }

    protected function getMainType(): FormTypeInterface
    {
        return new LinkType();
    }

    protected function getTypes(): array
    {
        $backendRegistry = new BackendRegistry(['value' => $this->createMock(BackendInterface::class)]);
        $configRegistry = new ConfigRegistry(['value' => new Configuration('value', 'Value', [])]);

        return [
            new ContentBrowserDynamicType($backendRegistry, $configRegistry),
        ];
    }
}
