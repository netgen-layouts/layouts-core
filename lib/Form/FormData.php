<?php

namespace Netgen\BlockManager\Form;

class FormData
{
    /**
     * Definition object related to target and payload
     *
     * @var mixed
     */
    public $definition;

    /**
     * Target object that will be updated
     *
     * @var mixed
     */
    public $target;

    /**
     * One of the matching create or update structs
     *
     * @var mixed
     */
    public $payload;

    /**
     * Constructor.
     *
     * @param mixed $definition
     * @param mixed $target
     * @param mixed $payload
     */
    public function __construct($definition = null, $target = null, $payload = null)
    {
        $this->definition = $definition;
        $this->target = $target;
        $this->payload = $payload;
    }
}
