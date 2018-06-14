<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

final class UrlMapper extends Mapper
{
    public function getFormType(): string
    {
        return UrlType::class;
    }
}
