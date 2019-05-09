<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App\Item\ValueUrlGenerator;

use Netgen\Layouts\Item\ValueUrlGeneratorInterface;

final class MyValueTypeValueUrlGenerator implements ValueUrlGeneratorInterface
{
    /**
     * @param \Netgen\Layouts\Tests\App\Item\Value $object
     */
    public function generate(object $object): ?string
    {
        return '/value/' . $object->id . '/some/url';
    }
}
