<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;

final class DeleteTarget extends Controller
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
     * Deletes a rule target.
     */
    public function __invoke(Target $target): ViewInterface
    {
        $this->layoutResolverService->deleteTarget($target);

        return $this->buildView(
            $this->layoutResolverService->loadRuleDraft(
                $target->getRuleId()
            ),
            ViewInterface::CONTEXT_ADMIN
        );
    }
}
