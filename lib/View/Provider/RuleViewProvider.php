<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\View\View\RuleView;

/**
 * @implements \Netgen\Layouts\View\Provider\ViewProviderInterface<\Netgen\Layouts\API\Values\LayoutResolver\Rule>
 */
final class RuleViewProvider implements ViewProviderInterface
{
    public function provideView(object $value, array $parameters = []): RuleView
    {
        return new RuleView($value);
    }

    public function supports(object $value): bool
    {
        return $value instanceof Rule;
    }
}
