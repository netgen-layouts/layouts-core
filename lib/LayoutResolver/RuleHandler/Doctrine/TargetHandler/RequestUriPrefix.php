<?php

namespace Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

class RequestUriPrefix extends RoutePrefix
{
    /**
     * Returns the target identifier this handler handles.
     *
     * @return string
     */
    public function getTargetIdentifier()
    {
        return 'request_uri_prefix';
    }
}
