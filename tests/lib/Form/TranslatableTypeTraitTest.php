<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Form;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Parameters\Form\Extension\ParametersTypeExtension;
use Netgen\Layouts\Parameters\Form\Type\ParametersType;
use Netgen\Layouts\Tests\Block\Stubs\BlockDefinitionHandlerWithTranslatableCompoundParameter;
use Netgen\Layouts\Tests\Block\Stubs\BlockDefinitionHandlerWithUntranslatableCompoundParameter;
use Netgen\Layouts\Tests\Form\Stubs\TranslatableTypeStub;
use Netgen\Layouts\Tests\Parameters\Stubs\FormMapper;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\FormTypeInterface;

final class TranslatableTypeTraitTest extends FormTestCase
{
    /**
     * @covers \Netgen\Layouts\Form\TranslatableTypeTrait::disableUntranslatableForms
     */
    public function testDisableUntranslatableFormsWithTranslatableCompoundParameter(): void
    {
        $handler = new BlockDefinitionHandlerWithTranslatableCompoundParameter();
        $block = Block::fromArray(
            [
                'definition' => BlockDefinition::fromArray(
                    [
                        'parameterDefinitions' => $handler->getParameterDefinitions(),
                    ],
                ),
            ],
        );

        $form = $this->factory->create(
            TranslatableTypeStub::class,
            new BlockUpdateStruct(),
            ['block' => $block],
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
     * @covers \Netgen\Layouts\Form\TranslatableTypeTrait::disableUntranslatableForms
     */
    public function testDisableUntranslatableFormsWithUntranslatableCompoundParameter(): void
    {
        $handler = new BlockDefinitionHandlerWithUntranslatableCompoundParameter();
        $block = Block::fromArray(
            [
                'definition' => BlockDefinition::fromArray(
                    [
                        'parameterDefinitions' => $handler->getParameterDefinitions(),
                    ],
                ),
            ],
        );

        $form = $this->factory->create(
            TranslatableTypeStub::class,
            new BlockUpdateStruct(),
            ['block' => $block],
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

    protected function getMainType(): FormTypeInterface
    {
        return new TranslatableTypeStub();
    }

    protected function getTypeExtensions(): array
    {
        return [new ParametersTypeExtension()];
    }

    protected function getTypes(): array
    {
        $formMappers = [
            'text_line' => new FormMapper(),
            'compound_boolean' => new FormMapper(true),
        ];

        return [new ParametersType(new Container($formMappers))];
    }
}
