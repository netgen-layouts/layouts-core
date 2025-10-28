<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

/**
 * Serves as a central point for generating paths to CMS items.
 */
interface UrlGeneratorInterface
{
    final public const string TYPE_DEFAULT = 'default';

    final public const string TYPE_ADMIN = 'admin';

    /**
     * Returns the item path.
     *
     * @throws \Netgen\Layouts\Exception\Item\ItemException if URL could not be generated
     */
    public function generate(CmsItemInterface $item, string $type = self::TYPE_DEFAULT): string;
}
