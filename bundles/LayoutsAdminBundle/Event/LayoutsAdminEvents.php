<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Event;

final class LayoutsAdminEvents
{
    /**
     * This event will be dispatched when the request is matched as being an admin interface request.
     */
    public const ADMIN_MATCH = 'nglayouts.admin.match';

    /**
     * This event will be dispatched when the admin menu is being built.
     */
    public const CONFIGURE_MENU = 'nglayouts.admin.configure_menu';
}
