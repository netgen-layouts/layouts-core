<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

/**
 * Serves as a central point for generating paths to CMS items.
 */
interface UrlGeneratorInterface
{
    public const TYPE_DEFAULT = 'default';

    public const TYPE_ADMIN = 'admin';

    /**
     * Returns the item path.
     *
     * @throws \Netgen\Layouts\Exception\Item\ItemException if URL could not be generated
     */
    public function generate(CmsItemInterface $item, string $type = self::TYPE_DEFAULT): string;
}
