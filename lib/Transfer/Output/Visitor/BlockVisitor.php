<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

use function ksort;

/**
 * Block value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Block\Block
 *
 * @implements \Netgen\Layouts\Transfer\Output\VisitorInterface<\Netgen\Layouts\API\Values\Block\Block>
 */
final class BlockVisitor implements VisitorInterface
{
    public function __construct(
        private BlockService $blockService,
    ) {}

    public function accept(object $value): bool
    {
        return $value instanceof Block;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            'id' => $value->id->toString(),
            'definition_identifier' => $value->definition->identifier,
            'is_translatable' => $value->isTranslatable,
            'is_always_available' => $value->alwaysAvailable,
            'main_locale' => $value->mainLocale,
            'available_locales' => $value->availableLocales,
            'view_type' => $value->viewType,
            'item_view_type' => $value->itemViewType,
            'name' => $value->name,
            'placeholders' => [...$this->visitPlaceholders($value, $outputVisitor)],
            'parameters' => $this->visitParameters($value),
            'configuration' => [...$this->visitConfiguration($value, $outputVisitor)],
            'collections' => [...$this->visitCollections($value, $outputVisitor)],
        ];
    }

    /**
     * Visit the given $block placeholders into hash representation.
     *
     * @return \Generator<string, array<string, mixed>>
     */
    private function visitPlaceholders(Block $block, OutputVisitor $outputVisitor): Generator
    {
        foreach ($block->placeholders as $placeholder) {
            yield $placeholder->identifier => $outputVisitor->visit($placeholder);
        }
    }

    /**
     * Visit the given $block parameters into hash representation.
     *
     * @return array<string, array<string, mixed>>
     */
    private function visitParameters(Block $block): array
    {
        $parametersByLanguage = [
            $block->locale => [...$this->visitTranslationParameters($block)],
        ];

        foreach ($block->availableLocales as $availableLocale) {
            if ($availableLocale === $block->locale) {
                continue;
            }

            $translatedBlock = $this->blockService->loadBlock(
                $block->id,
                [$availableLocale],
                false,
            );

            $parametersByLanguage[$availableLocale] = [...$this->visitTranslationParameters($translatedBlock)];
        }

        ksort($parametersByLanguage);

        return $parametersByLanguage;
    }

    /**
     * Return parameters for the given $block.
     *
     * @return \Generator<string, mixed>
     */
    private function visitTranslationParameters(Block $block): Generator
    {
        foreach ($block->parameters as $parameter) {
            $definition = $parameter->getParameterDefinition();
            $exportedValue = $definition->type->export($definition, $parameter->getValue());

            yield $parameter->getName() => $exportedValue;
        }
    }

    /**
     * Visit the given $block configuration into hash representation.
     *
     * @return \Generator<string, mixed>
     */
    private function visitConfiguration(Block $block, OutputVisitor $outputVisitor): Generator
    {
        foreach ($block->configs as $config) {
            yield $config->configKey => $outputVisitor->visit($config);
        }
    }

    /**
     * Visit the given $block collections into hash representation.
     *
     * @return \Generator<string, array<string, mixed>>
     */
    private function visitCollections(Block $block, OutputVisitor $outputVisitor): Generator
    {
        foreach ($block->collections as $identifier => $collection) {
            yield $identifier => $outputVisitor->visit($collection);
        }
    }
}
