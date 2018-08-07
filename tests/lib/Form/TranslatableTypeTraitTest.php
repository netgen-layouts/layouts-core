<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Form;

use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithTranslatableCompoundParameter;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandlerWithUntranslatableCompoundParameter;
use Netgen\BlockManager\Tests\Form\Stubs\TranslatableTypeStub;
use Netgen\BlockManager\Tests\Parameters\Stubs\FormMapper;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;

final class TranslatableTypeTraitTest extends FormTestCase
{
    public function getMainType(): FormTypeInterface
    {
        return new TranslatableTypeStub();
    }

    public function getTypeExtensions(): array
    {
        return [new ParametersTypeExtension()];
    }

    public function getTypes(): array
    {
        $formMappers = [
            'text_line' => new FormMapper(),
            'compound_boolean' => new FormMapper(true),
        ];

        return [new ParametersType($formMappers)];
    }

    /**
     * @covers \Netgen\BlockManager\Form\TranslatableTypeTrait::disableUntranslatableForms
     */
    public function testDisableUntranslatableFormsWithTranslatableCompoundParameter(): void
    {
        $handler = new BlockDefinitionHandlerWithTranslatableCompoundParameter();
        $block = Block::fromArray(
            [
                'definition' => BlockDefinition::fromArray(
                    [
                        'parameterDefinitions' => $handler->getParameterDefinitions(),
                    ]
                ),
            ]
        );

        $form = $this->factory->create(
            TranslatableTypeStub::class,
            new BlockUpdateStruct(),
            ['block' => $block]
        );

        self::assertTrue($form->get('name')->isDisabled());

        $paramsForm = $form->get('parameters');
        self::assertFalse($paramsForm->isDisabled());

        self::assertFalse($paramsForm->get('compound')->isDisabled());
        self::assertFalse($paramsForm->get('compound')->get('inner')->isDisabled());

        self::assertFalse($paramsForm->get('css_class')->isDisabled());
        self::assertTrue($paramsForm->get('css_id')->isDisabled());
    }

    /**
     * @covers \Netgen\BlockManager\Form\TranslatableTypeTrait::disableUntranslatableForms
     */
    public function testDisableUntranslatableFormsWithUntranslatableCompoundParameter(): void
    {
        $handler = new BlockDefinitionHandlerWithUntranslatableCompoundParameter();
        $block = Block::fromArray(
            [
                'definition' => BlockDefinition::fromArray(
                    [
                        'parameterDefinitions' => $handler->getParameterDefinitions(),
                    ]
                ),
            ]
        );

        $form = $this->factory->create(
            TranslatableTypeStub::class,
            new BlockUpdateStruct(),
            ['block' => $block]
        );

        self::assertTrue($form->get('name')->isDisabled());

        $paramsForm = $form->get('parameters');
        self::assertFalse($paramsForm->isDisabled());

        self::assertTrue($paramsForm->get('compound')->isDisabled());
        self::assertTrue($paramsForm->get('compound')->get('inner')->isDisabled());

        self::assertFalse($paramsForm->get('css_class')->isDisabled());
        self::assertTrue($paramsForm->get('css_id')->isDisabled());
        self::assertTrue($paramsForm->get('other')->isDisabled());
    }
}
