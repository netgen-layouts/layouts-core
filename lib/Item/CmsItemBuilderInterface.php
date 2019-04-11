<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

/**
 * Item builder is a service used as a central point for building instances of CmsItemInterface
 * from provided CMS values.
 */
interface CmsItemBuilderInterface
{
    /**
     * Builds the CMS item from provided value object coming from the CMS.
     *
     * @throws \Netgen\Layouts\Exception\Item\ValueException if value converter does not exist
     */
    public function build(object $object): CmsItemInterface;
}
