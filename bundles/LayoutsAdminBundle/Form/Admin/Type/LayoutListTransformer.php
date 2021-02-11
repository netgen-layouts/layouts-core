<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type;

use Netgen\Layouts\API\Values\Layout\LayoutList;
use Symfony\Component\Form\DataTransformerInterface;

final class LayoutListTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value): LayoutList
    {
        return new LayoutList($value);
    }
}
