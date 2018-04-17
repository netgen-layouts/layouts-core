<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Stubs;

use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class TargetType implements TargetTypeInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $value;

    public function __construct($type, $value = null)
    {
        $this->type = $type;
        $this->value = $value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getConstraints()
    {
        return [new Constraints\NotBlank()];
    }

    public function provideValue(Request $request)
    {
        return $this->value;
    }
}
