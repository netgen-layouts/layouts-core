<?php

namespace Netgen\BlockManager\View;

interface CacheableViewInterface extends ViewInterface
{
    /**
     * Returns if the view is cacheable.
     *
     * @return bool
     */
    public function isCacheable();

    /**
     * Sets if the view is cacheable or not.
     *
     * @param bool $isCacheable
     */
    public function setIsCacheable($isCacheable);

    /**
     * Returns the max age.
     *
     * @return int
     */
    public function getMaxAge();

    /**
     * Sets the max age.
     *
     * @param int $maxAge
     */
    public function setMaxAge($maxAge);

    /**
     * Returns the shared max age.
     *
     * @return int
     */
    public function getSharedMaxAge();

    /**
     * Sets the shared max age.
     *
     * @param int $sharedMaxAge
     */
    public function setSharedMaxAge($sharedMaxAge);

    /**
     * Returns if this view should overwrite already existing caching headers
     * that might've been set in the response.
     *
     * @return int
     */
    public function overwriteHeaders();

    /**
     * Sets if this view should overwrite already existing caching headers
     * that might've been set in the response.
     *
     * @param bool $overwriteHeaders
     */
    public function setOverwriteHeaders($overwriteHeaders);
}
