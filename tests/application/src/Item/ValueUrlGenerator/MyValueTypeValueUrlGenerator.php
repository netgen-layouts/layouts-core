<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App\Item\ValueUrlGenerator;

use Netgen\Layouts\Item\ExtendedValueUrlGeneratorInterface;

/**
 * @implements \Netgen\Layouts\Item\ExtendedValueUrlGeneratorInterface<\Netgen\Layouts\Tests\App\Item\Value>
 */
final class MyValueTypeValueUrlGenerator implements ExtendedValueUrlGeneratorInterface
{
    public function generateDefaultUrl(object $object): string
    {
        return '/value/' . $object->id . '/some/url';
    }

    public function generateAdminUrl(object $object): string
    {
        return '/admin/value/' . $object->id . '/some/url';
    }

    public function generate(object $object): string
    {
        return $this->generateDefaultUrl($object);
    }
}
