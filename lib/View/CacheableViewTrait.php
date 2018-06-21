<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View;

trait CacheableViewTrait
{
    /**
     * @var bool
     */
    private $isCacheable = false;

    /**
     * @var int
     */
    private $sharedMaxAge = 0;

    /**
     * Returns if the view is cacheable.
     */
    public function isCacheable(): bool
    {
        return $this->isCacheable;
    }

    /**
     * Sets if the view is cacheable or not.
     */
    public function setIsCacheable(bool $isCacheable): void
    {
        $this->isCacheable = $isCacheable;
    }

    /**
     * Returns the shared max age.
     */
    public function getSharedMaxAge(): int
    {
        return $this->sharedMaxAge;
    }

    /**
     * Sets the shared max age.
     */
    public function setSharedMaxAge(int $sharedMaxAge): void
    {
        $this->sharedMaxAge = $sharedMaxAge;
    }
}
