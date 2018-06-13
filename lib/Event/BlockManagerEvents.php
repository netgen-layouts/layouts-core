<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Event;

final class BlockManagerEvents
{
    /**
     * This event will be dispatched when the view object is being built.
     */
    const BUILD_VIEW = 'ngbm.view.build_view';

    /**
     * This event will be dispatched when the view object is being rendered.
     */
    const RENDER_VIEW = 'ngbm.view.render_view';
}
