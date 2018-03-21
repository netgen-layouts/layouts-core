<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ItemBuilderInterface;
use Netgen\BlockManager\Item\ItemInterface as CmsItem;
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

    public function __construct(
        ItemLoaderInterface $itemLoader,
        ItemBuilderInterface $itemBuilder
    ) {
        $this->itemLoader = $itemLoader;
        $this->itemBuilder = $itemBuilder;
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
        if (!$object instanceof CollectionItem) {
            $cmsItem = !$object instanceof CmsItem ?
                $this->itemBuilder->build($object) :
                $object;

            return new Result(
                array(
                    'item' => $cmsItem,
                    'collectionItem' => null,
                    'type' => Result::TYPE_DYNAMIC,
                    'position' => $position,
                )
            );
        }

        try {
            $cmsItem = $this->itemLoader->load($object->getValue(), $object->getValueType());
        } catch (ItemException $e) {
            $cmsItem = new NullItem(
                array(
                    'value' => $object->getValue(),
                )
            );
        }

        return new Result(
            array(
                'item' => $cmsItem,
                'collectionItem' => $object,
                'type' => Result::TYPE_MANUAL,
                'position' => $position,
            )
        );
    }
}
