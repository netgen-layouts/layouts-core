<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\Form;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\Form\DesignEditType;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Mapper\TextLineMapper;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DesignEditTypeTest extends FormTestCase
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

        $handler = new BlockDefinitionHandler(['design']);

        $this->definition = new BlockDefinition(
            [
                'identifier' => 'block_definition',
                'handler' => $handler,
                'forms' => [
                    'design' => new Form(
                        [
                            'identifier' => 'design',
                            'type' => DesignEditType::class,
                        ]
                    ),
                ],
                'viewTypes' => [
                    'large' => new ViewType(
                        [
                            'identifier' => 'large',
                            'name' => 'Large',
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(
                                    [
                                        'identifier' => 'standard',
                                        'name' => 'Standard',
                                    ]
                                ),
                                'other' => new ItemViewType(
                                    [
                                        'identifier' => 'other',
                                        'name' => 'Other',
                                    ]
                                ),
                            ],
                        ]
                    ),
                    'small' => new ViewType(
                        [
                            'identifier' => 'large',
                            'name' => 'Large',
                            'itemViewTypes' => [
                                'standard' => new ItemViewType(
                                    [
                                        'identifier' => 'standard',
                                        'name' => 'Standard',
                                    ]
                                ),
                            ],
                        ]
                    ),
                ],
                'parameterDefinitions' => $handler->getParameterDefinitions(),
            ]
        );

        $this->block = new Block(['definition' => $this->definition, 'mainLocale' => 'en']);
    }

    public function getMainType(): FormTypeInterface
    {
        return new DesignEditType();
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
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::buildView
     * @covers \Netgen\BlockManager\Block\Form\EditType::addParametersForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::processViewTypeConfig
     */
    public function testSubmitValidData(): void
    {
        $submittedData = [
            'parameters' => [
                'css_class' => 'Some CSS class',
            ],
            'view_type' => 'large',
            'item_view_type' => 'standard',
        ];

        $struct = new BlockUpdateStruct(['locale' => 'en']);

        $form = $this->factory->create(
            DesignEditType::class,
            $struct,
            ['block' => $this->block]
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());

        $this->assertSame('large', $struct->viewType);
        $this->assertSame('standard', $struct->itemViewType);
        $this->assertSame(['css_class' => 'Some CSS class'], $struct->getParameterValues());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($submittedData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

        foreach (array_keys($submittedData['parameters']) as $key) {
            $this->assertArrayHasKey($key, $children['parameters']);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::buildView
     * @covers \Netgen\BlockManager\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addParametersForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::processViewTypeConfig
     */
    public function testDisableUntranslatableFormsOnNonMainLocale(): void
    {
        $form = $this->factory->create(
            DesignEditType::class,
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

        $this->assertTrue($form->get('view_type')->isDisabled());
        $this->assertTrue($form->get('item_view_type')->isDisabled());

        $this->assertFalse($form->get('parameters')->isDisabled());
        $this->assertTrue($form->get('parameters')->get('css_class')->isDisabled());
        $this->assertTrue($form->get('parameters')->get('css_id')->isDisabled());
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::buildForm
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::buildView
     * @covers \Netgen\BlockManager\Block\Form\EditType::addBlockNameForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addParametersForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::addViewTypeForm
     * @covers \Netgen\BlockManager\Block\Form\EditType::processViewTypeConfig
     */
    public function testDisableUntranslatableFormsOnMainLocale(): void
    {
        $form = $this->factory->create(
            DesignEditType::class,
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

        $this->assertFalse($form->get('view_type')->isDisabled());
        $this->assertFalse($form->get('item_view_type')->isDisabled());

        $this->assertFalse($form->get('parameters')->isDisabled());
        $this->assertFalse($form->get('parameters')->get('css_class')->isDisabled());
        $this->assertFalse($form->get('parameters')->get('css_id')->isDisabled());
    }

    /**
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::configureOptions
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
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::configureOptions
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
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::configureOptions
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
     * @covers \Netgen\BlockManager\Block\Form\DesignEditType::configureOptions
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
