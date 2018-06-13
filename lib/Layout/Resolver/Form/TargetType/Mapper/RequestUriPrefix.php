<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class RequestUriPrefix extends Mapper
{
    public function getFormType()
    {
        return TextType::class;
    }
}
