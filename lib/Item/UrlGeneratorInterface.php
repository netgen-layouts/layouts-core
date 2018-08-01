<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item;

/**
 * Serves as a central point for generating paths to CMS items.
 */
interface UrlGeneratorInterface
{
    /**
     * Returns the CMS item path.
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException if URL could not be generated
     */
    public function generate(CmsItemInterface $item): string;
}
