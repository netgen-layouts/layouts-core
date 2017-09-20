<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Stubs;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class ConditionType implements ConditionTypeInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $matches = true;

    /**
     * Constructor.
     *
     * @param string $type
     * @param bool $matches
     */
    public function __construct($type, $matches = true)
    {
        $this->type = $type;
        $this->matches = $matches;
    }

    /**
     * Returns the condition type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the constraints that will be used to validate the condition value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints()
    {
        return array(new Constraints\NotBlank());
    }

    /**
     * Returns if this request matches the provided value.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $value
     *
     * @return bool
     */
    public function matches(Request $request, $value)
    {
        return $this->matches;
    }
}
