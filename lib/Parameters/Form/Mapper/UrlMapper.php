<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

final class UrlMapper extends Mapper
{
    public function getFormType(): string
    {
        return UrlType::class;
    }
}
