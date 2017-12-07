<?php

namespace Netgen\BlockManager\Parameters\ParameterType\ItemLink;

use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\ItemLoaderInterface;

class RemoteIdConverter
{
    const NULL_LINK = 'null://0';

    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    private $itemLoader;

    public function __construct(ItemLoaderInterface $itemLoader)
    {
        $this->itemLoader = $itemLoader;
    }

    /**
     * Converts the value_type://value_id format of the item reference to value_type://remote_id.
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
        $link = parse_url($link);

        if (!is_array($link) || !isset($link['host']) || !isset($link['scheme'])) {
            return self::NULL_LINK;
        }

        try {
            $item = $this->itemLoader->load($link['host'], str_replace('-', '_', $link['scheme']));

            return $link['scheme'] . '://' . $item->getRemoteId();
        } catch (ItemException $e) {
            // Do nothing
        }

        return self::NULL_LINK;
    }

    /**
     * Converts the value_type://remote_id format of the item reference to value_type://value_id.
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
        $link = parse_url($link);

        if (!is_array($link) || !isset($link['host']) || !isset($link['scheme'])) {
            return self::NULL_LINK;
        }

        try {
            $item = $this->itemLoader->loadByRemoteId($link['host'], str_replace('-', '_', $link['scheme']));

            return $link['scheme'] . '://' . $item->getValueId();
        } catch (ItemException $e) {
            // Do nothing
        }

        return self::NULL_LINK;
    }
}
