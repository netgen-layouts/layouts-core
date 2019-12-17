<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\View\ViewInterface;

final class CopyRule extends AbstractController
{
    /**
     * @var \Netgen\Layouts\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    public function __construct(LayoutResolverService $layoutResolverService)
    {
        $this->layoutResolverService = $layoutResolverService;
    }

    /**
     * Copies a rule. Rule is added to a position below the copied one, and
     * deactivated by default.
     */
    public function __invoke(Rule $rule): ViewInterface
    {
        $this->denyAccessUnlessGranted('nglayouts:mapping:edit');

        $updateStruct = $this->layoutResolverService->newRuleMetadataUpdateStruct();
        $updateStruct->priority = $rule->getPriority() + 1;

        $copiedRule = $this->layoutResolverService->updateRuleMetadata(
            $this->layoutResolverService->copyRule($rule),
            $updateStruct
        );

        if ($copiedRule->isEnabled()) {
            $copiedRule = $this->layoutResolverService->disableRule($copiedRule);
        }

        return $this->buildView($copiedRule, ViewInterface::CONTEXT_ADMIN);
    }
}
