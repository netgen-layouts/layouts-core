<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;

final class CreateRuleDraft extends Controller
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
     * Creates a new rule draft from published state.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function __invoke(Rule $rule)
    {
        $createdDraft = $this->layoutResolverService->createDraft($rule, true);

        return $this->buildView($createdDraft, ViewInterface::CONTEXT_ADMIN);
    }
}
