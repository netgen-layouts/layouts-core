<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\Item\VisibilityResolverInterface;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;

/**
 * This class builds a result object from the collection item.
 */
final class ResultItemBuilder
{
    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    /**
     * @var \Netgen\BlockManager\Item\ItemBuilderInterface
     */
    private $itemBuilder;

    /**
     * @var \Netgen\BlockManager\Collection\Item\VisibilityResolverInterface
     */
    private $visibilityResolver;

    public function __construct(
        ItemLoaderInterface $itemLoader,
        ItemBuilderInterface $itemBuilder,
        VisibilityResolverInterface $visibilityResolver
    ) {
        $this->itemLoader = $itemLoader;
        $this->itemBuilder = $itemBuilder;
        $this->visibilityResolver = $visibilityResolver;
    }

    /**
     * Returns the result for provided object.
     *
     * Object can be a collection item, which only holds the reference
     * to the value (ID and type), or a value itself.
     *
     * @param mixed $object
     * @param int $position
     *
     * @return \Netgen\BlockManager\Collection\Result\Result
     */
    public function build($object, $position)
    {
        if (!$object instanceof Item) {
            return new Result(
                array(
                    'item' => !$object instanceof ItemInterface ?
                        $this->itemBuilder->build($object) :
                        $object,
                    'collectionItem' => null,
                    'type' => Result::TYPE_DYNAMIC,
                    'position' => $position,
                    'isVisible' => true,
                    'invisibilityReason' => null,
                )
            );
        }

        try {
            $item = $this->itemLoader->load($object->getValue(), $object->getValueType());
        } catch (ItemException $e) {
            $item = new NullItem(
                array(
                    'value' => $object->getValue(),
                )
            );
        }

        $resultVisible = true;
        $invisibilityReason = null;
        if (!$item instanceof NullItem) {
            list($resultVisible, $invisibilityReason) = $this->isResultVisible($item, $object);
        }

        return new Result(
            array(
                'item' => $item,
                'collectionItem' => $object,
                'type' => Result::TYPE_MANUAL,
                'position' => $position,
                'isVisible' => $resultVisible,
                'invisibilityReason' => $invisibilityReason,
            )
        );
    }

    /**
     * Returns if the result built from CMS item and collection item will be
     * visible or not.
     *
     * Returned result is an array specifying the visibility as the first entry,
     * and the reason for *invisibility* as the second entry, reasons being one
     * of: hidden by CMS (if the CMS entity itself is hidden), hidden by
     * configuration (if hidden by the options available in the interface), or
     * hidden by code (if the item is hidden programmatically by using visibility
     * voters).
     *
     * @param \Netgen\BlockManager\Item\ItemInterface $cmsItem
     * @param \Netgen\BlockManager\API\Values\Collection\Item $collectionItem
     *
     * @return array
     */
    private function isResultVisible(ItemInterface $cmsItem, Item $collectionItem)
    {
        if (!$cmsItem->isVisible()) {
            return array(false, Result::HIDDEN_BY_CMS);
        }

        if (!$collectionItem->isVisible()) {
            return array(false, Result::HIDDEN_BY_CONFIG);
        }

        $visible = $this->visibilityResolver->isVisible($collectionItem);

        return array($visible, $visible ? null : Result::HIDDEN_BY_CODE);
    }
}
