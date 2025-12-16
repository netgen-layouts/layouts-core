<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\View\View\RuleTargetView;

/**
 * @implements \Netgen\Layouts\View\Provider\ViewProviderInterface<\Netgen\Layouts\API\Values\LayoutResolver\Target>
 */
final class RuleTargetViewProvider implements ViewProviderInterface
{
    public function provideView(object $value, array $parameters = []): RuleTargetView
    {
        return new RuleTargetView($value);
    }

    public function supports(object $value): bool
    {
        return $value instanceof Target;
    }
}
