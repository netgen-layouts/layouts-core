<?php

namespace Netgen\BlockManager\Value;

use Netgen\BlockManager\ValueObject;

class Value extends ValueObject
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $isVisible;

    /**
     * @var mixed
     */
    protected $object;

    /**
     * Returns the external value ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the external value type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the external value name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns if the external value is visible.
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * Returns the external value object.
     *
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }
}
