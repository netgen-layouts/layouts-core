<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;

final class DeleteCondition extends Controller
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
     * Deletes a rule condition.
     */
    public function __invoke(Condition $condition): ViewInterface
    {
        $this->layoutResolverService->deleteCondition($condition);

        return $this->buildView(
            $this->layoutResolverService->loadRuleDraft(
                $condition->getRuleId()
            ),
            ViewInterface::CONTEXT_ADMIN
        );
    }
}
