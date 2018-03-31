<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Item as CollectionItem;
use Netgen\BlockManager\Item\ItemLoaderInterface;

final class ManualCollectionRunner implements CollectionRunnerInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    public function __construct(ItemLoaderInterface $itemLoader)
    {
        $this->itemLoader = $itemLoader;
    }

    public function __invoke(Collection $collection, $offset, $limit)
    {
        $itemCount = 0;

        foreach ($collection->getItems() as $collectionItem) {
            if ($itemCount >= $limit) {
                return;
            }

            if ($collectionItem->getPosition() < $offset) {
                continue;
            }

            $manualItem = $this->getManualItem($collectionItem);
            if (!$manualItem->isValid()) {
                continue;
            }

            yield new Result($collectionItem->getPosition(), $manualItem);

            ++$itemCount;
        }
    }

    public function count(Collection $collection)
    {
        $totalCount = 0;

        foreach ($collection->getItems() as $collectionItem) {
            $manualItem = $this->getManualItem($collectionItem);
            if (!$manualItem->isValid()) {
                continue;
            }

            ++$totalCount;
        }

        return $totalCount;
    }

    /**
     * Builds and returns an object representing the manual CMS item, i.e. item
     * whose type and value are stored in a collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $collectionItem
     *
     * @return \Netgen\BlockManager\Collection\Result\ManualItem
     */
    private function getManualItem(CollectionItem $collectionItem)
    {
        $cmsItem = $this->itemLoader->load(
            $collectionItem->getValue(),
            $collectionItem->getValueType()
        );

        return new ManualItem($cmsItem, $collectionItem);
    }
}
