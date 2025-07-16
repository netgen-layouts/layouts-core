<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Value;

use Netgen\Layouts\Utils\HydratorTrait;
use Stringable;

use function sprintf;

final class LinkValue implements Stringable
{
    use HydratorTrait;

    public const LINK_TYPE_URL = 'url';

    public const LINK_TYPE_RELATIVE_URL = 'relative_url';

    public const LINK_TYPE_EMAIL = 'email';

    public const LINK_TYPE_PHONE = 'phone';

    public const LINK_TYPE_INTERNAL = 'internal';

    private string $linkType = '';

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
    public function getLinkType(): string
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
