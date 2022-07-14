<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\Controller\AbstractController;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\View\ViewInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;

use function is_string;
use function sprintf;

final class LinkLayout extends AbstractController
{
    private LayoutResolverService $layoutResolverService;

    private LayoutService $layoutService;

    public function __construct(LayoutResolverService $layoutResolverService, LayoutService $layoutService)
    {
        $this->layoutResolverService = $layoutResolverService;
        $this->layoutService = $layoutService;
    }

    /**
     * Updates the linked layout of the rule.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided layout does not exist
     */
    public function __invoke(Rule $rule, Request $request): ViewInterface
    {
        $this->denyAccessUnlessGranted(
            'nglayouts:mapping:edit',
            [
                'rule_group' => $rule->getRuleGroupId()->toString(),
            ],
        );

        $layoutId = $request->request->get('layout_id');
        if (!is_string($layoutId)) {
            throw new BadStateException('layout_id', 'Valid layout ID needs to be specified.');
        }

        try {
            $layout = $this->layoutService->loadLayout(Uuid::fromString($layoutId));
        } catch (NotFoundException $e) {
            throw new BadStateException(
                'layout_id',
                sprintf(
                    'Layout with UUID "%s" does not exist.',
                    $layoutId,
                ),
                $e,
            );
        }

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = $layout->getId();

        $updatedRule = $this->layoutResolverService->updateRule(
            $rule,
            $ruleUpdateStruct,
        );

        return $this->buildView($updatedRule, ViewInterface::CONTEXT_ADMIN);
    }
}
