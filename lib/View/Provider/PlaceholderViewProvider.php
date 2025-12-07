<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\View\View\PlaceholderView;
use Netgen\Layouts\View\ViewInterface;

use function array_key_exists;

/**
 * @implements \Netgen\Layouts\View\Provider\ViewProviderInterface<\Netgen\Layouts\API\Values\Block\Placeholder>
 */
final class PlaceholderViewProvider implements ViewProviderInterface
{
    public function provideView(mixed $value, array $parameters = []): ViewInterface
    {
        if (!array_key_exists('block', $parameters)) {
            throw ViewProviderException::noParameter('placeholder', 'block');
        }

        if (!$parameters['block'] instanceof Block) {
            throw ViewProviderException::invalidParameter('placeholder', 'block', Block::class);
        }

        return new PlaceholderView($value, $parameters['block']);
    }

    public function supports(mixed $value): bool
    {
        return $value instanceof Placeholder;
    }
}
