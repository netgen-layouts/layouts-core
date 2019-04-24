<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Block value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Block\Block
 */
final class BlockVisitor implements VisitorInterface
{
    /**
     * @var \Netgen\Layouts\API\Service\BlockService
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

    /**
     * @param \Netgen\Layouts\API\Values\Block\Block $value
     * @param \Netgen\Layouts\Transfer\Output\VisitorInterface|null $subVisitor
     *
     * @return mixed
     */
    public function visit($value, ?VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

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
            'placeholders' => iterator_to_array($this->visitPlaceholders($value, $subVisitor)),
            'parameters' => $this->visitParameters($value, $subVisitor),
            'configuration' => iterator_to_array($this->visitConfiguration($value, $subVisitor)),
            'collections' => iterator_to_array($this->visitCollections($value, $subVisitor)),
        ];
    }

    /**
     * Visit the given $block placeholders into hash representation.
     */
    private function visitPlaceholders(Block $block, VisitorInterface $subVisitor): Generator
    {
        foreach ($block->getPlaceholders() as $placeholder) {
            yield $placeholder->getIdentifier() => $subVisitor->visit($placeholder);
        }
    }

    /**
     * Visit the given $block parameters into hash representation.
     */
    private function visitParameters(Block $block, VisitorInterface $subVisitor): array
    {
        $parametersByLanguage = [
            $block->getLocale() => iterator_to_array(
                $this->visitTranslationParameters($block, $subVisitor)
            ),
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

            $parametersByLanguage[$availableLocale] = iterator_to_array(
                $this->visitTranslationParameters(
                    $translatedBlock,
                    $subVisitor
                )
            );
        }

        ksort($parametersByLanguage);

        return $parametersByLanguage;
    }

    /**
     * Return parameters for the given $block.
     */
    private function visitTranslationParameters(Block $block, VisitorInterface $subVisitor): Generator
    {
        foreach ($block->getParameters() as $parameter) {
            yield $parameter->getName() => $subVisitor->visit($parameter);
        }
    }

    /**
     * Visit the given $block configuration into hash representation.
     */
    private function visitConfiguration(Block $block, VisitorInterface $subVisitor): Generator
    {
        foreach ($block->getConfigs() as $config) {
            yield $config->getConfigKey() => $subVisitor->visit($config);
        }
    }

    /**
     * Visit the given $block collections into hash representation.
     */
    private function visitCollections(Block $block, VisitorInterface $subVisitor): Generator
    {
        foreach ($block->getCollections() as $identifier => $collection) {
            yield $identifier => $subVisitor->visit($collection);
        }
    }
}
