<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

use function iterator_to_array;
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
    private BlockService $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    public function accept(object $value): bool
    {
        return $value instanceof Block;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            'id' => $value->getId()->toString(),
            'definition_identifier' => $value->getDefinition()->getIdentifier(),
            'is_translatable' => $value->isTranslatable(),
            'is_always_available' => $value->isAlwaysAvailable(),
            'main_locale' => $value->getMainLocale(),
            'available_locales' => $value->getAvailableLocales(),
            'view_type' => $value->getViewType(),
            'item_view_type' => $value->getItemViewType(),
            'name' => $value->getName(),
            'placeholders' => iterator_to_array($this->visitPlaceholders($value, $outputVisitor)),
            'parameters' => $this->visitParameters($value),
            'configuration' => iterator_to_array($this->visitConfiguration($value, $outputVisitor)),
            'collections' => iterator_to_array($this->visitCollections($value, $outputVisitor)),
        ];
    }

    /**
     * Visit the given $block placeholders into hash representation.
     *
     * @return \Generator<string, array<string, mixed>>
     */
    private function visitPlaceholders(Block $block, OutputVisitor $outputVisitor): Generator
    {
        foreach ($block->getPlaceholders() as $placeholder) {
            yield $placeholder->getIdentifier() => $outputVisitor->visit($placeholder);
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
            $block->getLocale() => iterator_to_array(
                $this->visitTranslationParameters($block),
            ),
        ];

        foreach ($block->getAvailableLocales() as $availableLocale) {
            if ($availableLocale === $block->getLocale()) {
                continue;
            }

            $translatedBlock = $this->blockService->loadBlock(
                $block->getId(),
                [$availableLocale],
                false,
            );

            $parametersByLanguage[$availableLocale] = iterator_to_array(
                $this->visitTranslationParameters($translatedBlock),
            );
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
        foreach ($block->getParameters() as $parameter) {
            $definition = $parameter->getParameterDefinition();
            $exportedValue = $definition->getType()->export($definition, $parameter->getValue());

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
        foreach ($block->getConfigs() as $config) {
            yield $config->getConfigKey() => $outputVisitor->visit($config);
        }
    }

    /**
     * Visit the given $block collections into hash representation.
     *
     * @return \Generator<string, array<string, mixed>>
     */
    private function visitCollections(Block $block, OutputVisitor $outputVisitor): Generator
    {
        foreach ($block->getCollections() as $identifier => $collection) {
            yield $identifier => $outputVisitor->visit($collection);
        }
    }
}
