<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

/**
 * CMS item is an entity wrapping a value coming from a CMS. It is used as a generic
 * concept making it possible for Netgen Layouts to work and integrate with any CMS.
 */
interface CmsItemInterface
{
    /**
     * Returns the scalar ID of the value from CMS that this item wraps.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Returns the scalar remote ID of the value from CMS that this item wraps.
     *
     * @return mixed
     */
    public function getRemoteId();

    /**
     * Returns the type of the value from CMS that this item wraps.
     */
    public function getValueType(): string;

    /**
     * Returns the name of the value from CMS that this item wraps.
     */
    public function getName(): string;

    /**
     * Returns if the value from CMS is visible.
     */
    public function isVisible(): bool;

    /**
     * Returns the original value as supplied by the CMS or null if value does not exist.
     */
    public function getObject(): ?object;
}
