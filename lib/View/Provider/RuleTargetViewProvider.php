<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\View\View\RuleTargetView;
use Netgen\Layouts\View\ViewInterface;

final class RuleTargetViewProvider implements ViewProviderInterface
{
    public function provideView(mixed $value, array $parameters = []): ViewInterface
    {
        return new RuleTargetView($value);
    }

    public function supports(mixed $value): bool
    {
        return $value instanceof Target;
    }
}
