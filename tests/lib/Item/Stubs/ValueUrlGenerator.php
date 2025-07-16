<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\Stubs;

use Netgen\Layouts\Item\ExtendedValueUrlGeneratorInterface;

/**
 * @implements \Netgen\Layouts\Item\ExtendedValueUrlGeneratorInterface<\Netgen\Layouts\Tests\Item\Stubs\Value>
 */
final class ValueUrlGenerator implements ExtendedValueUrlGeneratorInterface
{
    public function generateDefaultUrl(object $object): string
    {
        return '/item-url';
    }

    public function generateAdminUrl(object $object): string
    {
        return '/admin/item-url';
    }

    public function generate(object $object): string
    {
        return $this->generateDefaultUrl($object);
    }
}
