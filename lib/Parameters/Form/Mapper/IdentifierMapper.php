<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class IdentifierMapper extends Mapper
{
    public function getFormType()
    {
        return TextType::class;
    }
}
