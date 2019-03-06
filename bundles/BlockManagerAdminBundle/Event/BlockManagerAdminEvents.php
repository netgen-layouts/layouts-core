<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Event;

final class BlockManagerAdminEvents
{
    /**
     * This event will be dispatched when the request is matched as being an admin interface request.
     */
    public const ADMIN_MATCH = 'ngbm.admin.match';

    /**
     * This event will be dispatched when the admin menu is being built.
     */
    public const CONFIGURE_MENU = 'ngbm.admin.configure_menu';
}
