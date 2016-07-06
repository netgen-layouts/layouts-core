<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LayoutResolverController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected $layoutResolverService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutResolverService $layoutResolverService
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     */
    public function __construct(
        LayoutResolverService $layoutResolverService,
        LayoutService $layoutService
    ) {
        $this->layoutResolverService = $layoutResolverService;
        $this->layoutService = $layoutService;
    }

    /**
     * Updates the rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided layout does not exist.
     *
     * @return \Netgen\BlockManager\Serializer\Values\View
     */
    public function updateRule(RuleDraft $rule, Request $request)
    {
        $layoutId = $request->request->get('layout_id');

        // null means we don't update the layout
        // 0 means we remove the layout from the rule
        if ($layoutId !== null && $layoutId !== 0) {
            try {
                $this->layoutService->loadLayout($layoutId);
            } catch (NotFoundException $e) {
                throw new BadStateException('layout_id', 'Layout does not exist.', $e);
            }
        }

        $ruleUpdateStruct = $this->layoutResolverService->newRuleUpdateStruct();
        $ruleUpdateStruct->layoutId = $layoutId;
        $ruleUpdateStruct->comment = $request->request->get('comment');
        $ruleUpdateStruct->priority = $request->request->get('priority');

        $this->layoutResolverService->updateRule($rule, $ruleUpdateStruct);

        return new View($rule, Version::API_V1);
    }
}
