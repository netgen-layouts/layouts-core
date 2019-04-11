<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Mapper;

use Netgen\Layouts\Parameters\Form\Mapper;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

final class EmailMapper extends Mapper
{
    public function getFormType(): string
    {
        return EmailType::class;
    }
}
