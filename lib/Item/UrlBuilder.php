<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\Exception\Item\ValueException;

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
     * @throws \Netgen\BlockManager\Exception\Item\ValueException if value URL builder does not exist
     *
     * @return string
     */
    public function getUrl(ItemInterface $item)
    {
        if (!isset($this->valueUrlBuilders[$item->getValueType()])) {
            throw ValueException::noValueUrlBuilder($item->getValueType());
        }

        return $this->valueUrlBuilders[$item->getValueType()]->getUrl(
            $item->getObject()
        );
    }
}
