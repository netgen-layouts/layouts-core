<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View;

trait CacheableViewTrait
{
    /**
     * @var bool
     */
    private $isCacheable = true;

    /**
     * @var int
     */
    private $sharedMaxAge;

    /**
     * Returns if the view is cacheable.
     *
     * @return bool
     */
    public function isCacheable(): bool
    {
        return $this->isCacheable;
    }

    /**
     * Sets if the view is cacheable or not.
     *
     * @param bool $isCacheable
     */
    public function setIsCacheable(bool $isCacheable): void
    {
        $this->isCacheable = (bool) $isCacheable;
    }

    /**
     * Returns the shared max age.
     *
     * @return int|null
     */
    public function getSharedMaxAge(): ?int
    {
        return $this->sharedMaxAge;
    }

    /**
     * Sets the shared max age.
     *
     * @param int $sharedMaxAge
     */
    public function setSharedMaxAge(int $sharedMaxAge): void
    {
        $this->sharedMaxAge = (int) $sharedMaxAge;
    }
}
