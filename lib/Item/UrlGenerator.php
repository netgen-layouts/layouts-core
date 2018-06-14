<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item;

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
        $this->valueUrlGenerators = array_filter(
            $valueUrlGenerators,
            function (ValueUrlGeneratorInterface $valueUrlGenerator): bool {
                return true;
            }
        );
    }

    public function generate(ItemInterface $item): ?string
    {
        if ($item instanceof NullItem) {
            return null;
        }

        if (!isset($this->valueUrlGenerators[$item->getValueType()])) {
            throw ItemException::noValueType($item->getValueType());
        }

        return $this->valueUrlGenerators[$item->getValueType()]->generate(
            $item->getObject()
        );
    }
}
