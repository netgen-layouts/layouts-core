<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\Page\LayoutInfo;
use Netgen\BlockManager\View\View\LayoutInfoView;

class LayoutInfoViewProvider implements ViewProviderInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected $layoutResolverService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutResolverService $layoutResolverService
     */
    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Provides the view.
     *
     * @param mixed $valueObject
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView($valueObject, array $parameters = array())
    {
        /** @var \Netgen\BlockManager\API\Values\Page\LayoutInfo $valueObject */
        $layoutInfoView = new LayoutInfoView($valueObject);

        $layoutInfoView->addParameters(
            array(
                'rule_count' => $this->layoutResolverService->getRuleCount($valueObject),
            )
        );

        return $layoutInfoView;
    }

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param mixed $valueObject
     *
     * @return bool
     */
    public function supports($valueObject)
    {
        return $valueObject instanceof LayoutInfo;
    }
}
