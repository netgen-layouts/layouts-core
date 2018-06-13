<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\ParameterType\ItemLink;

use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\NullItem;

final class RemoteIdConverter
{
    private static $nullLink = 'null://0';

    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    public function __construct(ItemLoaderInterface $itemLoader)
    {
        $this->itemLoader = $itemLoader;
    }

    /**
     * Converts the value_type://value format of the item reference to value_type://remote_id.
     * This is useful for various export/import operations between different systems.
     *
     * If the conversion cannot be done, (for example, because item does not exist), a reference to
     * the so called null item will be returned.
     *
     * @param string $link
     *
     * @return string
     */
    public function convertToRemoteId($link)
    {
        $link = is_string($link) ? parse_url($link) : $link;

        if (!is_array($link) || !isset($link['host']) || !isset($link['scheme'])) {
            return self::$nullLink;
        }

        $item = $this->itemLoader->load($link['host'], str_replace('-', '_', $link['scheme']));
        if ($item instanceof NullItem) {
            return self::$nullLink;
        }

        return $link['scheme'] . '://' . $item->getRemoteId();
    }

    /**
     * Converts the value_type://remote_id format of the item reference to value_type://value.
     * This is useful for various export/import operations between different systems.
     *
     * If the conversion cannot be done, (for example, because item does not exist), a reference to
     * the so called null item will be returned.
     *
     * @param string $link
     *
     * @return string
     */
    public function convertFromRemoteId($link)
    {
        $link = is_string($link) ? parse_url($link) : $link;

        if (!is_array($link) || !isset($link['host']) || !isset($link['scheme'])) {
            return self::$nullLink;
        }

        $item = $this->itemLoader->loadByRemoteId($link['host'], str_replace('-', '_', $link['scheme']));
        if ($item instanceof NullItem) {
            return self::$nullLink;
        }

        return $link['scheme'] . '://' . $item->getValue();
    }
}
