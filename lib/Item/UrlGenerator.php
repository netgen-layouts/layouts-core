<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

use Netgen\Layouts\Exception\Item\ItemException;
use Psr\Container\ContainerInterface;

use function in_array;

final class UrlGenerator implements UrlGeneratorInterface
{
    private ContainerInterface $valueUrlGenerators;

    public function __construct(ContainerInterface $valueUrlGenerators)
    {
        $this->valueUrlGenerators = $valueUrlGenerators;
    }

    public function generate(CmsItemInterface $item, string $type = self::TYPE_DEFAULT): string
    {
        if (!in_array($type, [self::TYPE_DEFAULT, self::TYPE_ADMIN], true)) {
            throw ItemException::invalidUrlType($item->getValueType(), $type);
        }

        $object = $item->getObject();
        if ($item instanceof NullCmsItem || $object === null) {
            return '';
        }

        $valueUrlGenerator = $this->getValueUrlGenerator($item->getValueType());

        if ($valueUrlGenerator instanceof ExtendedValueUrlGeneratorInterface) {
            if ($type === self::TYPE_ADMIN) {
                return $valueUrlGenerator->generateAdminUrl($object) ?? '';
            }

            return $valueUrlGenerator->generateDefaultUrl($object) ?? '';
        }

        return $valueUrlGenerator->generate($object) ?? '';
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
