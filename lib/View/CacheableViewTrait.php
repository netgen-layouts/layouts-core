<?php

namespace Netgen\BlockManager\View;

trait CacheableViewTrait
{
    /**
     * @var bool
     */
    protected $isCacheable = true;

    /**
     * @var int
     */
    protected $sharedMaxAge;

    /**
     * Returns if the view is cacheable.
     *
     * @return bool
     */
    public function isCacheable()
    {
        return $this->isCacheable;
    }

    /**
     * Sets if the view is cacheable or not.
     *
     * @param bool $isCacheable
     */
    public function setIsCacheable($isCacheable)
    {
        $this->isCacheable = (bool) $isCacheable;
    }

    /**
     * Returns the shared max age.
     *
     * @return int
     */
    public function getSharedMaxAge()
    {
        return $this->sharedMaxAge;
    }

    /**
     * Sets the shared max age.
     *
     * @param int $sharedMaxAge
     */
    public function setSharedMaxAge($sharedMaxAge)
    {
        $this->sharedMaxAge = (int) $sharedMaxAge;
    }
}
