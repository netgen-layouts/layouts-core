<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class PathInfo extends Mapper
{
    public function getFormType(): string
    {
        return TextType::class;
    }
}
