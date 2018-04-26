<?php

namespace Netgen\BlockManager\Parameters\Value;

use Netgen\BlockManager\Value;

final class LinkValue extends Value
{
    const LINK_TYPE_URL = 'url';

    const LINK_TYPE_EMAIL = 'email';

    const LINK_TYPE_PHONE = 'phone';

    const LINK_TYPE_INTERNAL = 'internal';

    /**
     * @var string|null
     */
    protected $linkType;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var string
     */
    protected $linkSuffix;

    /**
     * @var bool
     */
    protected $newWindow;

    /**
     * Returns the link type or null if this is an empty link.
     *
     * @return string|null
     */
    public function getLinkType()
    {
        return $this->linkType;
    }

    /**
     * Returns the link value.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Returns the link suffix.
     *
     * @return string
     */
    public function getLinkSuffix()
    {
        return $this->linkSuffix;
    }

    /**
     * Returns if the link should be opened in new window.
     *
     * @return bool
     */
    public function getNewWindow()
    {
        return (bool) $this->newWindow;
    }
}
