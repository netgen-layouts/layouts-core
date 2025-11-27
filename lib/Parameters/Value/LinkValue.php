<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Value;

use Netgen\Layouts\Utils\HydratorTrait;
use Stringable;

use function sprintf;

final class LinkValue implements Stringable
{
    use HydratorTrait;

    /**
     * Returns the link type.
     */
    public private(set) ?LinkType $linkType = null;

    /**
     * Returns the link value.
     */
    public private(set) string $link = '';

    /**
     * Returns the link suffix.
     */
    public private(set) string $linkSuffix = '';

    /**
     * Returns if the link should be opened in new window.
     */
    public private(set) bool $newWindow = false;

    public function __toString(): string
    {
        return sprintf('%s%s', $this->link, $this->linkSuffix);
    }
}
