<?php

declare(strict_types=1);

namespace Netgen\Layouts\Event;

use Netgen\Layouts\View\ViewInterface;
use Symfony\Contracts\EventDispatcher\Event;

use function sprintf;

/**
 * This event will be dispatched when the view object is being built.
 *
 * You can subscribe to the FQCN of the event, which will be triggered when
 * building every view type, or you can subscribe to the specific view type
 * event by using the static getEventName() method to generate the event name.
 */
final class BuildViewEvent extends Event
{
    private const string BUILD_VIEW = 'nglayouts.view.build_view';

    public function __construct(
        /**
         * The view object that is being built.
         */
        public private(set) ViewInterface $view,
    ) {}

    public static function getEventName(string $viewType): string
    {
        return sprintf('%s.%s', self::BUILD_VIEW, $viewType);
    }
}
