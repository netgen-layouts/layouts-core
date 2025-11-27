<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

use Netgen\Layouts\Exception\Item\ItemException;
use Psr\Container\ContainerInterface;

final class UrlGenerator implements UrlGeneratorInterface
{
    public function __construct(
        private ContainerInterface $valueUrlGenerators,
    ) {}

    public function generate(CmsItemInterface $item, UrlType $type = UrlType::Default): string
    {
        if ($item instanceof NullCmsItem || $item->object === null) {
            return '';
        }

        $valueUrlGenerator = $this->getValueUrlGenerator($item->valueType);

        if ($type === UrlType::Admin) {
            return $valueUrlGenerator->generateAdminUrl($item->object) ?? '';
        }

        return $valueUrlGenerator->generateDefaultUrl($item->object) ?? '';
    }

    /**
     * Returns the value URL generator for provided value type from the collection.
     *
     * @throws \Netgen\Layouts\Exception\Item\ItemException If the value URL generator does not exist or is not of correct type
     *
     * @return \Netgen\Layouts\Item\ValueUrlGeneratorInterface<object>
     */
    private function getValueUrlGenerator(string $valueType): ValueUrlGeneratorInterface
    {
        if (!$this->valueUrlGenerators->has($valueType)) {
            throw ItemException::noValueUrlGenerator($valueType);
        }

        $valueUrlGenerator = $this->valueUrlGenerators->get($valueType);
        if (!$valueUrlGenerator instanceof ValueUrlGeneratorInterface) {
            throw ItemException::noValueUrlGenerator($valueType);
        }

        return $valueUrlGenerator;
    }
}
