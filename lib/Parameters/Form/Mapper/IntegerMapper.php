<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

final class IntegerMapper extends Mapper
{
    public function getFormType(): string
    {
        return IntegerType::class;
    }
}
