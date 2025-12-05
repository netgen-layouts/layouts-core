<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App\Item\ValueUrlGenerator;

use Netgen\Layouts\Item\ValueUrlGeneratorInterface;

/**
 * @implements \Netgen\Layouts\Item\ValueUrlGeneratorInterface<\Netgen\Layouts\Tests\App\Item\TestValue>
 */
final class TestValueTypeValueUrlGenerator implements ValueUrlGeneratorInterface
{
    public function generateDefaultUrl(object $object): string
    {
        return '/value/' . $object->id . '/some/url';
    }

    public function generateAdminUrl(object $object): string
    {
        return '/admin/value/' . $object->id . '/some/url';
    }
}
