<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\Exception\InvalidInterfaceException;

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
        if (!isset($this->valueUrlGenerators[$item->getValueType()])) {
            return;
        }

        return $this->valueUrlGenerators[$item->getValueType()]->generate(
            $item->getObject()
        );
    }
}
