<?php

declare(strict_types=1);

namespace Netgen\Layouts\Event;

use Netgen\Layouts\View\ViewInterface;
use Symfony\Contracts\EventDispatcher\Event;

use function sprintf;

/**
 * This event will be dispatched when the view object is being rendered.
 *
 * You can subscribe to the FQCN of the event, which will be triggered when
 * rendering every view type, or you can subscribe to the specific view type
 * event by using the static getEventName() method to generate the event name.
 */
final class RenderViewEvent extends Event
{
    private const string RENDER_VIEW = 'nglayouts.view.render_view';

    public function __construct(
        /**
         * The view object that is being rendered.
         */
        public private(set) ViewInterface $view,
    ) {}

    public static function getEventName(string $viewType): string
    {
        return sprintf('%s.%s', self::RENDER_VIEW, $viewType);
    }
}
