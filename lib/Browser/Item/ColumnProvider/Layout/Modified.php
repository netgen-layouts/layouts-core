<?php

namespace Netgen\BlockManager\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\Browser\Item\Layout\LayoutInterface;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;

final class Modified implements ColumnValueProviderInterface
{
    /**
     * @var string
     */
    private $dateFormat;

    /**
     * @param string $dateFormat
     */
    public function __construct($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    public function getValue(ItemInterface $item)
    {
        if (!$item instanceof LayoutInterface) {
            return null;
        }

        return $item->getLayout()->getModified()->format(
            $this->dateFormat
        );
    }
}
