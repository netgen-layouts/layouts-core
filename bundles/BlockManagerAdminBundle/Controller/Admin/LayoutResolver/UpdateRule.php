<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerAdminBundle\Controller\Admin\Controller;
use Symfony\Component\HttpFoundation\Request;

final class UpdateRule extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutResolverService $layoutResolverService, LayoutService $layoutService)
    {
        $this->layoutResolverService = $layoutResolverService;
        $this->layoutService = $layoutService;
    }

    /**
     * Updates the rule.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided layout does not exist
     */
    public function __invoke(Rule $rule, Request $request): ViewInterface
    {
        $layoutId = $request->request->get('layout_id');
        $layoutId = $layoutId !== null ? trim($layoutId) : null;

        $comment = $request->request->get('comment');
        $comment = $comment !== null ? trim($comment) : null;

        // null means we don't update the layout
        // empty ("" or "0") means we remove the layout from the rule
        if ($layoutId !== null && !empty($layoutId)) {
            try {
                $this->layoutService->loadLayout($layoutId);
            } catch (NotFoundException $e) {
                throw new BadStateException(
                    'layout_id',
                    sprintf(
                        'Layout with ID "%s" does not exist.',
                        $layoutId
                    ),
                    $e
                );
            }
        }

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->comment = $comment;

        if ($layoutId !== null) {
            $ruleUpdateStruct->layoutId = !empty($layoutId) ? $layoutId : 0;
        }

        $updatedRule = $this->layoutResolverService->updateRule(
            $rule,
            $ruleUpdateStruct
        );

        return $this->buildView($updatedRule, ViewInterface::CONTEXT_ADMIN);
    }
}
