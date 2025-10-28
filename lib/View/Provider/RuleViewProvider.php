<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\View\View\RuleView;
use Netgen\Layouts\View\ViewInterface;

final class RuleViewProvider implements ViewProviderInterface
{
    public function provideView(mixed $value, array $parameters = []): ViewInterface
    {
        return new RuleView($value);
    }

    public function supports(mixed $value): bool
    {
        return $value instanceof Rule;
    }
}
