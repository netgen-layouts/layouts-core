<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Item;
use IteratorIterator;

class ResultBuilderIterator extends IteratorIterator
{
    /**
     * Builds the result object.
     *
     * @return \Netgen\BlockManager\Collection\Result\Result
     */
    public function current()
    {
        /** @var \Netgen\BlockManager\Item\ItemInterface $item */
        $item = parent::current();
        $position = parent::key();

        if ($item instanceof CollectionItemBased) {
            $collectionItem = $item->getCollectionItem();

            return new Result(
                array(
                    'item' => $item,
                    'collectionItem' => $collectionItem,
                    'type' => $collectionItem->getType() === Item::TYPE_MANUAL ?
                        Result::TYPE_MANUAL :
                        Result::TYPE_OVERRIDE,
                    'position' => $position,
                )
            );
        }

        return new Result(
            array(
                'item' => $item,
                'collectionItem' => null,
                'type' => Result::TYPE_DYNAMIC,
                'position' => $position,
            )
        );
    }
}
