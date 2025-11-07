<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Value;

use Netgen\Layouts\Utils\HydratorTrait;
use Stringable;

use function sprintf;

final class LinkValue implements Stringable
{
    use HydratorTrait;

    private ?LinkType $linkType = null;

    private string $link = '';

    private string $linkSuffix = '';

    private bool $newWindow = false;

    public function __toString(): string
    {
        return sprintf('%s%s', $this->link, $this->linkSuffix);
    }

    /**
     * Returns the link type.
     */
    public function getLinkType(): ?LinkType
    {
        return $this->linkType;
    }

    /**
     * Returns the link value.
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * Returns the link suffix.
     */
    public function getLinkSuffix(): string
    {
        return $this->linkSuffix;
    }

    /**
     * Returns if the link should be opened in new window.
     */
    public function getNewWindow(): bool
    {
        return $this->newWindow;
    }
}
