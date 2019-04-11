<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class PathInfoPrefix extends Mapper
{
    public function getFormType(): string
    {
        return TextType::class;
    }
}
