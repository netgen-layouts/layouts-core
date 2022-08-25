<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

final class HiddenMapper extends Mapper
{
    public function getFormType(): string
    {
        return HiddenType::class;
    }
}
