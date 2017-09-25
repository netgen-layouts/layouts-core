<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Event;

final class BlockManagerAdminEvents
{
    /**
     * This event will be dispatched when the request is matched as being an admin interface request.
     */
    const ADMIN_MATCH = 'ngbm.admin.match';
}
