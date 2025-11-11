<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Stubs;

use Netgen\Layouts\Parameters\Form\Mapper;
use Symfony\Component\Form\Extension\Core\Type\FormType;

final class EmptyFormMapper extends Mapper
{
    public function getFormType(): string
    {
        return FormType::class;
    }
}
