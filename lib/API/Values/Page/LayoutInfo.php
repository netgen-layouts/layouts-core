<?php

namespace Netgen\BlockManager\API\Values\Page;

use Netgen\BlockManager\API\Values\Value;

interface LayoutInfo extends Value
{
    /**
     * Returns the layout ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the layout type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the layout human readable name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns when was the layout created.
     *
     * @return \DateTime
     */
    public function getCreated();

    /**
     * Returns when was the layout last updated.
     *
     * @return \DateTime
     */
    public function getModified();

    /**
     * Returns the status of the layout.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Returns if the layout is shared.
     *
     * @return bool
     */
    public function isShared();
}
