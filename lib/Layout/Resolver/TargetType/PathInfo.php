<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

class PathInfo implements TargetTypeInterface
{
    public function getType()
    {
        return 'path_info';
    }

    public function getConstraints()
    {
        return [
            new Constraints\NotBlank(),
            new Constraints\Type(['type' => 'string']),
        ];
    }

    public function provideValue(Request $request)
    {
        return $request->getPathInfo();
    }
}
