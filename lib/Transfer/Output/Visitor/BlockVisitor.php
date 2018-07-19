<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Block value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Block\Block
 */
final class BlockVisitor implements VisitorInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    public function accept($value): bool
    {
        return $value instanceof Block;
    }

    public function visit($block, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Block\Block $block */

        return [
            'id' => $block->getId(),
            'definition_identifier' => $block->getDefinition()->getIdentifier(),
            'is_translatable' => $block->isTranslatable(),
            'is_always_available' => $block->isAlwaysAvailable(),
            'main_locale' => $block->getMainLocale(),
            'available_locales' => $block->getAvailableLocales(),
            'view_type' => $block->getViewType(),
            'item_view_type' => $block->getItemViewType(),
            'name' => $block->getName(),
            'placeholders' => $this->visitPlaceholders($block, $subVisitor),
            'parameters' => $this->visitParameters($block, $subVisitor),
            'configuration' => $this->visitConfiguration($block, $subVisitor),
            'collections' => $this->visitCollections($block, $subVisitor),
        ];
    }

    /**
     * Visit the given $block placeholders into hash representation.
     */
    private function visitPlaceholders(Block $block, VisitorInterface $subVisitor): array
    {
        $hash = [];

        foreach ($block->getPlaceholders() as $placeholder) {
            $hash[$placeholder->getIdentifier()] = $subVisitor->visit($placeholder);
        }

        return $hash;
    }

    /**
     * Visit the given $block parameters into hash representation.
     */
    private function visitParameters(Block $block, VisitorInterface $subVisitor): array
    {
        $parametersByLanguage = [
            $block->getLocale() => $this->visitTranslationParameters($block, $subVisitor),
        ];

        foreach ($block->getAvailableLocales() as $availableLocale) {
            if ($availableLocale === $block->getLocale()) {
                continue;
            }

            $translatedBlock = $this->blockService->loadBlock(
                $block->getId(),
                [$availableLocale],
                false
            );

            $parametersByLanguage[$availableLocale] = $this->visitTranslationParameters(
                $translatedBlock,
                $subVisitor
            );
        }

        ksort($parametersByLanguage);

        return $parametersByLanguage;
    }

    /**
     * Return parameters for the given $block.
     */
    private function visitTranslationParameters(Block $block, VisitorInterface $subVisitor): array
    {
        $hash = [];

        foreach ($block->getParameters() as $parameter) {
            $hash[$parameter->getName()] = $subVisitor->visit($parameter);
        }

        return $hash;
    }

    /**
     * Visit the given $block configuration into hash representation.
     */
    private function visitConfiguration(Block $block, VisitorInterface $subVisitor): array
    {
        $hash = [];

        foreach ($block->getConfigs() as $config) {
            $hash[$config->getConfigKey()] = $subVisitor->visit($config);
        }

        return $hash;
    }

    /**
     * Visit the given $block collections into hash representation.
     */
    private function visitCollections(Block $block, VisitorInterface $subVisitor): array
    {
        $hash = [];

        foreach ($block->getCollections() as $identifier => $collection) {
            $hash[$identifier] = $subVisitor->visit($collection);
        }

        return $hash;
    }
}
