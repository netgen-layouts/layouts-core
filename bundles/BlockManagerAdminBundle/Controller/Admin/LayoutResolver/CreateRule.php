<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;

final class CreateRule extends Controller
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
     * Creates a new rule.
     */
    public function __invoke(): ViewInterface
    {
        $createdRule = $this->layoutResolverService->createRule(
            $this->layoutResolverService->newRuleCreateStruct()
        );

        $createdRule = $this->layoutResolverService->publishRule(
            $createdRule
        );

        return $this->buildView($createdRule, ViewInterface::CONTEXT_ADMIN);
    }
}
