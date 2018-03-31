<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;

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
        $yieldCount = 0;

        foreach ($collection->getItems() as $collectionItem) {
            if ($yieldCount >= $limit) {
                return;
            }

            if ($collectionItem->getPosition() < $offset) {
                continue;
            }

            $cmsItem = $this->itemLoader->load(
                $collectionItem->getValue(),
                $collectionItem->getValueType()
            );

            if (!$collectionItem->isVisible() || !$cmsItem->isVisible() || $cmsItem instanceof NullItem) {
                continue;
            }

            yield new Result(
                $collectionItem->getPosition(),
                new ManualItem($cmsItem, $collectionItem)
            );

            ++$yieldCount;
        }
    }

    public function count(Collection $collection)
    {
        $totalCount = 0;

        foreach ($collection->getItems() as $collectionItem) {
            $cmsItem = $this->itemLoader->load(
                $collectionItem->getValue(),
                $collectionItem->getValueType()
            );

            if (!$collectionItem->isVisible() || !$cmsItem->isVisible() || $cmsItem instanceof NullItem) {
                continue;
            }

            ++$totalCount;
        }

        return $totalCount;
    }
}
