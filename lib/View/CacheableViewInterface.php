<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View;

interface CacheableViewInterface extends ViewInterface
{
    /**
     * Returns if the view is cacheable.
     */
    public function isCacheable(): bool;

    /**
     * Sets if the view is cacheable or not.
     */
    public function setIsCacheable(bool $isCacheable): void;

    /**
     * Returns the shared max age.
     */
    public function getSharedMaxAge(): ?int;

    /**
     * Sets the shared max age.
     */
    public function setSharedMaxAge(int $sharedMaxAge): void;
}
