<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\Browser\Item\Layout\LayoutInterface;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;

final class Created implements ColumnValueProviderInterface
{
    /**
     * @var string
     */
    private $dateFormat;

    /**
     * @param string $dateFormat
     */
    public function __construct(string $dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    public function getValue(ItemInterface $item): ?string
    {
        if (!$item instanceof LayoutInterface) {
            return null;
        }

        return $item->getLayout()->getCreated()->format(
            $this->dateFormat
        );
    }
}
