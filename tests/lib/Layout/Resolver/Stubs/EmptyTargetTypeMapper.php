<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Stubs;

use Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\FormType;

final class EmptyTargetTypeMapper extends Mapper
{
    public function getFormType(): string
    {
        return FormType::class;
    }
}
