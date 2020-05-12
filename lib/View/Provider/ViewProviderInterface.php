<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\View\ViewInterface;

/**
 * For every view, an instance of this interface needs to be implemented with
 * creates the view based on provided value and parameters.
 *
 * Basically, implementations of this interface are view factories.
 */
interface ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param mixed $value
     * @param array<string, mixed> $parameters
     */
    public function provideView($value, array $parameters = []): ViewInterface;

    /**
     * Returns if this view provider supports the given value.
     *
     * @param mixed $value
     */
    public function supports($value): bool;
}
