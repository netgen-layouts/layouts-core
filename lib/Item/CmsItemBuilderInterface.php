<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item;

/**
 * Item builder is a service used as a central point for building instances of CmsItemInterface
 * from provided CMS values.
 */
interface CmsItemBuilderInterface
{
    /**
     * Builds the CMS item from provided value object coming from the CMS.
     *
     * @param object $object
     *
     * @throws \Netgen\BlockManager\Exception\Item\ValueException if value converter does not exist
     *
     * @return \Netgen\BlockManager\Item\CmsItemInterface
     */
    public function build($object): CmsItemInterface;
}
