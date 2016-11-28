<?php

namespace Netgen\Bundle\BlockManagerBundle\Browser\Item\Renderer;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\Renderer\TemplateValueProviderInterface;

class LayoutTemplateValueProvider implements TemplateValueProviderInterface
{
    /**
     * Provides the values for template rendering.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     *
     * @return array
     */
    public function getValues(ItemInterface $item)
    {
        return array(
            'layout' => $item->getLayout(),
        );
    }
}
