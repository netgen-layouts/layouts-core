<?php

namespace Netgen\BlockManager\API\Values\Page;

interface Layout
{
    /**
     * Returns the layout ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the parent layout ID.
     *
     * @return int|string
     */
    public function getParentId();

    /**
     * Returns the layout identifier.
     *
     * @return string
     */
    public function getIdentifier();

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
     * Returns all zones from the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Zone[]
     */
    public function getZones();
}
