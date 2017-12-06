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

    public function convertToRemoteId($link)
    {
        $link = parse_url($link);

        if (!is_array($link) || !isset($link['host']) || !isset($link['scheme'])) {
            return self::NULL_LINK;
        }

        try {
            $item = $this->itemLoader->load($link['host'], str_replace('-', '_', $link['scheme']));

            return str_replace('_', '-', $item->getValueType()) . '://' . $item->getRemoteId();
        } catch (ItemException $e) {
            // Do nothing
        }

        return self::NULL_LINK;
    }

    public function convertFromRemoteId($link)
    {
        $link = parse_url($link);

        if (!is_array($link) || !isset($link['host']) || !isset($link['scheme'])) {
            return self::NULL_LINK;
        }

        try {
            $item = $this->itemLoader->loadByRemoteId($link['host'], str_replace('-', '_', $link['scheme']));

            return str_replace('_', '-', $item->getValueType()) . '://' . $item->getValueId();
        } catch (ItemException $e) {
            // Do nothing
        }

        return self::NULL_LINK;
    }
}
