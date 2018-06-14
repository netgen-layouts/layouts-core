<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View;

interface CacheableViewInterface extends ViewInterface
{
    /**
     * Returns if the view is cacheable.
     *
     * @return bool
     */
    public function isCacheable(): bool;

    /**
     * Sets if the view is cacheable or not.
     *
     * @param bool $isCacheable
     */
    public function setIsCacheable(bool $isCacheable): void;

    /**
     * Returns the shared max age.
     *
     * @return int
     */
    public function getSharedMaxAge(): ?int;

    /**
     * Sets the shared max age.
     *
     * @param int $sharedMaxAge
     */
    public function setSharedMaxAge(int $sharedMaxAge): void;
}
