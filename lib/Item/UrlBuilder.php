<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\Exception\InvalidInterfaceException;
use Netgen\BlockManager\Exception\Item\ValueException;

final class UrlBuilder implements UrlBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ValueUrlBuilderInterface[]
     */
    private $valueUrlBuilders;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\ValueUrlBuilderInterface[] $valueUrlBuilders
     */
    public function __construct(array $valueUrlBuilders = array())
    {
        foreach ($valueUrlBuilders as $valueUrlBuilder) {
            if (!$valueUrlBuilder instanceof ValueUrlBuilderInterface) {
                throw new InvalidInterfaceException(
                    'Value URL builder',
                    get_class($valueUrlBuilder),
                    ValueUrlBuilderInterface::class
                );
            }
        }

        $this->valueUrlBuilders = $valueUrlBuilders;
    }

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
