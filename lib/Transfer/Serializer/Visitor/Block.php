<?php

namespace Netgen\BlockManager\Transfer\Serializer\Visitor;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block as BlockValue;
use Netgen\BlockManager\Transfer\Serializer\Visitor;
use RuntimeException;

/**
 * Block value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Block\Block
 */
class Block extends Visitor
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    public function accept($value)
    {
        return $value instanceof BlockValue;
    }

    public function visit($block, Visitor $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Block\Block $block */

        return array(
            'id' => $block->getId(),
            'definition_identifier' => $block->getDefinition()->getIdentifier(),
            'status' => $this->getStatusString($block),
            'is_published' => $block->isPublished(),
            'view_type' => $block->getViewType(),
            'item_view_type' => $block->getItemViewType(),
            'name' => $block->getName(),
            'placeholders' => $this->visitPlaceholders($block, $subVisitor),
            'parameters' => $this->visitParameterValues($block, $subVisitor),
            'configuration' => $this->visitConfiguration($block, $subVisitor),
            'collections' => $this->visitCollections($block, $subVisitor),
        );
    }

    /**
     * Visit the given $block placeholders into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\Transfer\Serializer\Visitor $subVisitor
     *
     * @return array
     */
    private function visitPlaceholders(BlockValue $block, Visitor $subVisitor)
    {
        $placeholders = $block->getPlaceholders();
        if (empty($placeholders)) {
            return null;
        }

        $hash = array();

        foreach ($placeholders as $placeholder) {
            $hash[$placeholder->getIdentifier()] = $subVisitor->visit($placeholder);
        }

        return $hash;
    }

    /**
     * Visit the given $block parameters into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\Transfer\Serializer\Visitor $subVisitor
     *
     * @return array
     */
    private function visitParameterValues(BlockValue $block, Visitor $subVisitor)
    {
        $parameterValues = $block->getParameters();
        if (empty($parameterValues)) {
            return null;
        }

        $hash = array();

        foreach ($parameterValues as $parameterValue) {
            $hash[$parameterValue->getName()] = $subVisitor->visit($parameterValue);
        }

        return $hash;
    }

    /**
     * Visit the given $block configuration into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\Transfer\Serializer\Visitor $subVisitor
     *
     * @return array
     */
    private function visitConfiguration(BlockValue $block, Visitor $subVisitor)
    {
        $configs = $block->getConfigs();
        if (empty($configs)) {
            return null;
        }

        $hash = array();

        foreach ($configs as $config) {
            $hash[$config->getConfigKey()] = $subVisitor->visit($config);
        }

        return $hash;
    }

    /**
     * Visit the given $block collections into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param \Netgen\BlockManager\Transfer\Serializer\Visitor $subVisitor
     *
     * @return array
     */
    private function visitCollections(BlockValue $block, Visitor $subVisitor)
    {
        $hash = array();
        $collectionReferences = $this->blockService->loadCollectionReferences($block);

        foreach ($collectionReferences as $collectionReference) {
            $hash[$collectionReference->getIdentifier()] = $subVisitor->visit($collectionReference->getCollection());
        }

        return $hash;
    }
}
