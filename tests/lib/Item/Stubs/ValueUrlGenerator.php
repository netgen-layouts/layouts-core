<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\Stubs;

use Netgen\Layouts\Item\ValueUrlGeneratorInterface;

/**
 * @implements \Netgen\Layouts\Item\ValueUrlGeneratorInterface<\Netgen\Layouts\Tests\Item\Stubs\Value>
 */
final class ValueUrlGenerator implements ValueUrlGeneratorInterface
{
    public function generate(object $object): ?string
    {
        return '/item-url';
    }
}
