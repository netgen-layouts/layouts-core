<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;

final class EnableRule extends AbstractController
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Enables a rule.
     */
    public function __invoke(Rule $rule): ViewInterface
    {
        $this->denyAccessUnlessGranted('nglayouts:mapping:activate');

        $enabledRule = $this->layoutResolverService->enableRule($rule);

        return $this->buildView($enabledRule, ViewInterface::CONTEXT_ADMIN);
    }
}
