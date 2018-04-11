<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Stubs;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class ConditionType implements ConditionTypeInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $matches = true;

    public function __construct($type, $matches = true)
    {
        $this->type = $type;
        $this->matches = $matches;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getConstraints()
    {
        return array(new Constraints\NotBlank());
    }

    public function matches(Request $request, $value)
    {
        return $this->matches;
    }
}
