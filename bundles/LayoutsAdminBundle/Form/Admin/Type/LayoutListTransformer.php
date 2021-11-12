<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type;

use Netgen\Layouts\API\Values\Layout\LayoutList;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<
 *     \Netgen\Layouts\API\Values\Layout\LayoutList,
 *     \Netgen\Layouts\API\Values\Layout\Layout[]
 * >
 */
final class LayoutListTransformer implements DataTransformerInterface
{
    public function transform($value): ?array
    {
        return $value !== null ? $value->getLayouts() : null;
    }

    public function reverseTransform($value): ?LayoutList
    {
        return $value !== null ? new LayoutList($value) : null;
    }
}
