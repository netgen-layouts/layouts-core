<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\Stubs;

use Netgen\BlockManager\LayoutResolver\TargetBuilder\TargetBuilderInterface;
use Netgen\BlockManager\Tests\LayoutResolver\Stubs\Target as TargetStub;

class TargetBuilder implements TargetBuilderInterface
{
    /**
     * @var array
     */
    protected $values = array();

    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        $this->values = $values;
    }

    /**
     * Builds the target object that will be used to search for resolver rules.
     *
     * @return \Netgen\BlockManager\LayoutResolver\Target|null
     */
    public function buildTarget()
    {
        return new TargetStub($this->values);
    }
}
