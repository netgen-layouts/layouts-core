<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\Form;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\Form\FullEditType;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FullEditTypeTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    private $definition;

    /**
     * @var \Netgen\BlockManager\API\Values\Block\Block
     */
    private $block;

    public function setUp(): void
    {
        parent::setUp();

        $handler = new BlockDefinitionHandler();
        $this->definition = new BlockDefinition(
            [
                'identifier' => 'definition',
                'parameterDefinitions' => $handler->getParameterDefinitions(),
                'viewTypes' => [
                    'large' => new ViewType(
                        [
                            'name' => 'large',
                            'identifier' => 'large',
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(
                                    [
                                        'name' => 'standard',
                                        'identifier' => 'standard',
                                    ]
                                ),
                            ],
                        ]
                    ),
                    'small' => new ViewType(
                        [
                            'name' => 'small',
                            'identifier' => 'small',
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(
                                    [
                                        'name' => 'standard',
                                        'identifier' => 'standard',
                                    ]
                                ),
                            ],
                        ]
                    ),
                ],
            ]
        );

        $this->block = new Block(['definition' => $this->definition, 'mainLocale' => 'en']);
    }

    public function getMainType(): FormTypeInterface
    {
        return new FullEditType();
    }

    public function getTypeExtensions(): array
    {
        return [new ParametersTypeExtension()];
    }

    public function getTypes(): array
    {
        return [new ParametersType(['text_line' => new TextLineMapper()])];
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addParametersForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::processViewTypeConfig
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::buildView
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'parameters' => [
                'css_id' => 'Some CSS ID',
                'css_class' => 'Some CSS class',
            ],
            'view_type' => 'large',
            'item_view_type' => 'standard',
            'name' => 'My block',
        ];

        $struct = new BlockUpdateStruct(['locale' => 'en']);

        $form = $this->factory->create(
            FullEditType::class,
            $struct,
            ['block' => $this->block]
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());

        $this->assertSame('My block', $struct->name);
        $this->assertSame('large', $struct->viewType);
        $this->assertSame('standard', $struct->itemViewType);

        $this->assertSame(
            ['css_class' => 'Some CSS class', 'css_id' => 'Some CSS ID'],
            $struct->getParameterValues()
        );

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

        foreach (array_keys($submittedData['parameters']) as $key) {
            $this->assertArrayHasKey($key, $children['parameters']);
        }

        $this->assertArrayHasKey('block', $view->vars);
        $this->assertSame($this->block, $view->vars['block']);

        $this->assertArrayHasKey('parameter_view_types', $view->vars);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addParametersForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::processViewTypeConfig
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::buildView
     */
    public function testDisableUntranslatableFormsOnNonMainLocale(): void
    {
        $form = $this->factory->create(
            FullEditType::class,
            new BlockUpdateStruct(['locale' => 'hr']),
            [
                'block' => new Block(
                    [
                        'definition' => $this->definition,
                        'mainLocale' => 'en',
                    ]
                ),
            ]
        );

        $this->assertTrue($form->get('name')->isDisabled());
        $this->assertTrue($form->get('view_type')->isDisabled());
        $this->assertTrue($form->get('item_view_type')->isDisabled());

        $this->assertFalse($form->get('parameters')->isDisabled());
        $this->assertTrue($form->get('parameters')->get('css_class')->isDisabled());
        $this->assertTrue($form->get('parameters')->get('css_id')->isDisabled());
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addParametersForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::processViewTypeConfig
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::buildView
     */
    public function testDisableUntranslatableFormsOnMainLocale(): void
    {
        $form = $this->factory->create(
            FullEditType::class,
            new BlockUpdateStruct(['locale' => 'en']),
            [
                'block' => new Block(
                    [
                        'definition' => $this->definition,
                        'mainLocale' => 'en',
                    ]
                ),
            ]
        );

        $this->assertFalse($form->get('name')->isDisabled());
        $this->assertFalse($form->get('view_type')->isDisabled());
        $this->assertFalse($form->get('item_view_type')->isDisabled());

        $this->assertFalse($form->get('parameters')->isDisabled());
        $this->assertFalse($form->get('parameters')->get('css_class')->isDisabled());
        $this->assertFalse($form->get('parameters')->get('css_id')->isDisabled());
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::configureOptions
     */
    public function testConfigureOptions(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $struct = new BlockUpdateStruct();

        $options = $optionsResolver->resolve(
            [
                'block' => $this->block,
                'data' => $struct,
            ]
        );

        $this->assertSame($this->block, $options['block']);
        $this->assertSame($struct, $options['data']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @expectedExceptionMessage The required option "block" is missing.
     */
    public function testConfigureOptionsWithMissingBlock(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve();
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "block" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Block\Block", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidBlock(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'block' => '',
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\FullEditType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "data" with value "" is expected to be of type "Netgen\BlockManager\API\Values\Block\BlockUpdateStruct", but is of type "string".
     */
    public function testConfigureOptionsWithInvalidData(): void
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver->setDefined('data');

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'block' => $this->block,
                'data' => '',
            ]
        );
    }
}
