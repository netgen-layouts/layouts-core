<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\Exception\RuntimeException;

class UrlBuilder implements UrlBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ValueUrlBuilderInterface[]
     */
    protected $valueUrlBuilders;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\ValueUrlBuilderInterface[] $valueUrlBuilders
     */
    public function __construct(array $valueUrlBuilders = array())
    {
        $this->valueUrlBuilders = $valueUrlBuilders;
    }

    /**
     * Returns the item URL.
     *
     * @param \Netgen\BlockManager\Item\ItemInterface $item
     *
     * @return string
     */
    public function getUrl(ItemInterface $item)
    {
        if (!isset($this->valueUrlBuilders[$item->getValueType()])) {
            throw new RuntimeException(
                sprintf(
                    'Value URL builder for "%s" value type does not exist.',
                    $item->getValueType()
                )
            );
        }

        return $this->valueUrlBuilders[$item->getValueType()]->getUrl(
            $item->getObject()
        );
    }
}
