<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\Exception\InvalidInterfaceException;
use Netgen\BlockManager\Exception\Item\ItemException;

final class UrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var \Netgen\BlockManager\Item\ValueUrlGeneratorInterface[]
     */
    private $valueUrlGenerators;

    /**
     * @param \Netgen\BlockManager\Item\ValueUrlGeneratorInterface[] $valueUrlGenerators
     */
    public function __construct(array $valueUrlGenerators = [])
    {
        foreach ($valueUrlGenerators as $valueUrlGenerator) {
            if (!$valueUrlGenerator instanceof ValueUrlGeneratorInterface) {
                throw new InvalidInterfaceException(
                    'Value URL generator',
                    get_class($valueUrlGenerator),
                    ValueUrlGeneratorInterface::class
                );
            }
        }

        $this->valueUrlGenerators = $valueUrlGenerators;
    }

    public function generate(ItemInterface $item)
    {
        if ($item instanceof NullItem) {
            return;
        }

        if (!isset($this->valueUrlGenerators[$item->getValueType()])) {
            throw ItemException::noValueType($item->getValueType());
        }

        return $this->valueUrlGenerators[$item->getValueType()]->generate(
            $item->getObject()
        );
    }
}
