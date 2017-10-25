<?php

namespace Netgen\BlockManager\View\Fragment;

use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

final class BlockViewRenderer implements ViewRendererInterface
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface
     */
    private $cacheableResolver;

    /**
     * @var string
     */
    private $blockController;

    /**
     * @var array
     */
    private $supportedContexts;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface $cacheableResolver
     * @param string $blockController
     * @param array $supportedContexts
     */
    public function __construct(
        CacheableResolverInterface $cacheableResolver,
        $blockController,
        array $supportedContexts = array(ViewInterface::CONTEXT_DEFAULT)
    ) {
        $this->cacheableResolver = $cacheableResolver;
        $this->blockController = $blockController;
        $this->supportedContexts = $supportedContexts;
    }

    public function supportsView(ViewInterface $view)
    {
        if (!$view instanceof BlockViewInterface) {
            return false;
        }

        if (!in_array($view->getContext(), $this->supportedContexts, true)) {
            return false;
        }

        return $this->cacheableResolver->isCacheable($view->getBlock());
    }

    public function getController(ViewInterface $view)
    {
        /* @var \Netgen\BlockManager\View\View\BlockViewInterface $view */

        return new ControllerReference(
            $this->blockController,
            array(
                'blockId' => $view->getBlock()->getId(),
                'locale' => $view->getBlock()->getLocale(),
                'viewContext' => $view->getContext(),
                '_ngbm_status' => 'published',
            )
        );
    }
}
