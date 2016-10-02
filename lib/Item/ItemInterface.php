<?php

namespace Netgen\BlockManager\Item;

interface ItemInterface
{
    /**
     * Returns the external value ID.
     *
     * @return int|string
     */
    public function getValueId();

    /**
     * Returns the external value type.
     *
     * @return string
     */
    public function getValueType();

    /**
     * Returns the external value name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns if the external value is visible.
     *
     * @return bool
     */
    public function isVisible();

    /**
     * Returns the external value object.
     *
     * @return mixed
     */
    public function getObject();
}
