<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\Stubs;

use Netgen\Layouts\Item\ValueUrlGeneratorInterface;

/**
 * @implements \Netgen\Layouts\Item\ValueUrlGeneratorInterface<\Netgen\Layouts\Tests\Item\Stubs\Value>
 */
final class ValueUrlGenerator implements ValueUrlGeneratorInterface
{
    public function generateDefaultUrl(object $object): string
    {
        return '/item-url';
    }

    public function generateAdminUrl(object $object): string
    {
        return '/admin/item-url';
    }
}
