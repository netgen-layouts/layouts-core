<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Fragment;

use Netgen\BlockManager\Context\ContextInterface;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

final class BlockViewRenderer implements ViewRendererInterface
{
    /**
     * @var \Netgen\BlockManager\Context\ContextInterface
     */
    private $context;

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
    private $supportedViewContexts;

    public function __construct(
        ContextInterface $context,
        CacheableResolverInterface $cacheableResolver,
        string $blockController,
        array $supportedViewContexts = [ViewInterface::CONTEXT_DEFAULT]
    ) {
        $this->context = $context;
        $this->cacheableResolver = $cacheableResolver;
        $this->blockController = $blockController;
        $this->supportedViewContexts = $supportedViewContexts;
    }

    public function supportsView(ViewInterface $view): bool
    {
        if (!$view instanceof BlockViewInterface) {
            return false;
        }

        if (!in_array($view->getContext(), $this->supportedViewContexts, true)) {
            return false;
        }

        return $this->cacheableResolver->isCacheable($view->getBlock());
    }

    public function getController(ViewInterface $view): ControllerReference
    {
        /* @var \Netgen\BlockManager\View\View\BlockViewInterface $view */

        return new ControllerReference(
            $this->blockController,
            [
                'blockId' => $view->getBlock()->getId(),
                'locale' => $view->getBlock()->getLocale(),
                'viewContext' => $view->getContext(),
                'ngbmContext' => $this->context->all(),
                '_ngbm_status' => 'published',
            ]
        );
    }
}
