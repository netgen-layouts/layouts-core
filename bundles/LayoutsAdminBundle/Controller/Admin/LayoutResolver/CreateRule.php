<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\View\ViewInterface;
use Ramsey\Uuid\Uuid;

final class CreateRule extends AbstractController
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
     * Creates a new rule.
     */
    public function __invoke(): ViewInterface
    {
        $this->denyAccessUnlessGranted('nglayouts:mapping:edit');

        $createdRule = $this->layoutResolverService->createRule(
            $this->layoutResolverService->newRuleCreateStruct(),
            $this->layoutResolverService->loadRuleGroup(Uuid::fromString(RuleGroup::ROOT_UUID))
        );

        $createdRule = $this->layoutResolverService->publishRule(
            $createdRule
        );

        return $this->buildView($createdRule, ViewInterface::CONTEXT_ADMIN);
    }
}
